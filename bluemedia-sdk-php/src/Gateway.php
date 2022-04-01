<?php

namespace BlueMedia\OnlinePayments;

use BlueMedia\OnlinePayments\Action\ITN;
use BlueMedia\OnlinePayments\Action\PaywayList\Transformer;
use BlueMedia\OnlinePayments\Model\ItnIn;
use BlueMedia\OnlinePayments\Model\PaywayList;
use BlueMedia\OnlinePayments\Model\TransactionBackground;
use BlueMedia\OnlinePayments\Model\TransactionInit;
use BlueMedia\OnlinePayments\Model\TransactionStandard;
use BlueMedia\OnlinePayments\Util\EnvironmentRequirements;
use BlueMedia\OnlinePayments\Util\HttpClient;
use BlueMedia\OnlinePayments\Util\Logger;
use BlueMedia\OnlinePayments\Util\XMLParser;
use Exception;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;
use RuntimeException;
use SimpleXMLElement;
use XMLWriter;

class Gateway
{
    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';

    const PAYMENT_DOMAIN_SANDBOX = 'pay-accept.bm.pl';
    const PAYMENT_DOMAIN_LIVE = 'pay.bm.pl';

    const PAYMENT_ACTON_PAYMENT = '/payment';
    const PAYMENT_ACTON_PAYWAY_LIST = '/paywayList';

    const GET_MERCHANT_INFO = '/webapi/googlePayMerchantInfo';
    const GET_REGULATIONS = '/webapi/regulationsGet';

    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_NOT_CONFIRMED = 'NOTCONFIRMED';

    const DATETIME_FORMAT = 'YmdHis';
    const DATETIME_FORMAT_LONGER = 'Y-m-d H:i:s';
    const DATETIME_TIMEZONE = 'Europe/Warsaw';

    const HASH_MD5 = 'md5';
    const HASH_SHA1 = 'sha1';
    const HASH_SHA256 = 'sha256';
    const HASH_SHA512 = 'sha512';

    const PATTERN_PAYWAY = '@<!-- PAYWAY FORM BEGIN -->(.*)<!-- PAYWAY FORM END -->@Usi';
    const PATTERN_XML_ERROR = '@<error>(.*)</error>@Usi';
    const PATTERN_GENERAL_ERROR = '/error(.*)/si';
    const LOGGER_NAME = 'BM_SDK';
    const LOG_PATH = 'var/logs/bm-sdk';
    const MAX_LOG_FILES = 60;

    const GATEWAY_SMARTNEY_MIN = 100;
    const GATEWAY_SMARTNEY_MAX = 2500;
    /** @var string */
    private $response = '';

    /** @var string */
    protected static $serviceId = 0;

    /** @var string */
    protected static $hashingSalt = '';

    /** @var string */
    protected static $mode = self::MODE_SANDBOX;

    /** @var string */
    protected static $hashingAlgorithm = self::HASH_SHA256;

    /** @var string */
    protected static $hashingSeparator = '|';

    /**
     * List of supported hashing algorithms.
     *
     * @var array
     */
    protected $hashingAlgorithmSupported
        = [
            self::HASH_MD5    => 1,
            self::HASH_SHA1   => 1,
            self::HASH_SHA256 => 1,
            self::HASH_SHA512 => 1,
        ];

    /** @var HttpClient */
    protected static $httpClient;

    /**
     * Parse response from Payment System.
     *
     * @return TransactionBackground|string
     */
    private function parseResponse()
    {
        $this->isErrorResponse();

        if ($this->isPaywayFormResponse()) {
            preg_match_all(self::PATTERN_PAYWAY, $this->response, $data);

            Logger::log(
                Logger::INFO,
                'Got pay way form',
                [
                    'data'          => $data['1']['0'],
                    'full-response' => $this->response,
                ]
            );

            return htmlspecialchars_decode($data['1']['0']);
        }


        return $this->parseTransferResponse();
    }

    /**
     * Parses init transfer response.
     *
     * @return SimpleXMLElement
     * @throws RuntimeException
     */
    private function parseInitTransferResponse()
    {
        $xmlData = XMLParser::parse($this->response);

        if (isset($xmlData->confirmation) && (string)$xmlData->confirmation === ItnIn::CONFIRMATION_NOT_CONFIRMED) {
            throw new RuntimeException((string)$xmlData->reason);
        }

        return $xmlData;
    }

    /**
     * Parses transfer response.
     *
     * @return TransactionBackground
     * @throws RuntimeException
     */
    private function parseTransferResponse()
    {
        $xmlData = XMLParser::parse($this->response);

        $transactionBackground = new TransactionBackground();

        if (isset($xmlData->receiverNRB)) {
            $transactionBackground->setReceiverNrb((string)$xmlData->receiverNRB);
        }
        if (isset($xmlData->receiverName)) {
            $transactionBackground->setReceiverName((string)$xmlData->receiverName);
        }
        if (isset($xmlData->currency)) {
            $transactionBackground->setCurrency((string)$xmlData->currency);
        }
        if (isset($xmlData->title)) {
            $transactionBackground->setTitle((string)$xmlData->title);
        }

        $transactionBackground
            ->setReceiverAddress((string)$xmlData->receiverAddress)
            ->setOrderId((string)$xmlData->orderID)
            ->setAmount((string)$xmlData->amount)
            ->setRemoteId((string)$xmlData->remoteID)
            ->setBankHref((string)$xmlData->bankHref)
            ->setHash((string)$xmlData->hash);

        $transactionBackgroundHash = self::generateHash($transactionBackground->toArray());
        if ($transactionBackgroundHash !== $transactionBackground->getHash()) {
            Logger::log(
                Logger::EMERGENCY,
                sprintf(
                    'Received wrong hash, calculated hash "%s", received hash "%s"',
                    $transactionBackgroundHash,
                    $transactionBackground->getHash()
                ),
                [
                    'data'          => $transactionBackground->toArray(),
                    'full-response' => $this->response,
                ]
            );
            throw new RuntimeException('Received wrong hash!');
        }

        return $transactionBackground;
    }

    /**
     * Is error response.
     *
     * @return void
     */
    private function isErrorResponse()
    {
        if (preg_match_all(self::PATTERN_XML_ERROR, $this->response, $data)) {
            $xmlData = XMLParser::parse($this->response);
            Logger::log(
                Logger::EMERGENCY,
                sprintf('Got error: "%s", code: "%s"', $xmlData->name, $xmlData->statusCode),
                [
                    'data'          => $xmlData,
                    'full-response' => $this->response,
                ]
            );
            throw new RuntimeException((string)$xmlData->name);
        }

        if (preg_match_all(self::PATTERN_GENERAL_ERROR, $this->response, $data)) {
            throw new RuntimeException($this->response);
        }
    }

    /**
     * Is pay way form response.
     *
     * @return int
     */
    private function isPaywayFormResponse()
    {
        return preg_match_all(self::PATTERN_PAYWAY, $this->response, $data);
    }

    /**
     * Checks PHP required environment.
     *
     * @codeCoverageIgnore
     * @return void
     * @throws RuntimeException
     *
     */
    protected function checkPhpEnvironment()
    {
        if (EnvironmentRequirements::hasSupportedPhpVersion()) {
            throw new RuntimeException(sprintf('Required at least PHP version 7.0, current version "%s"', PHP_VERSION));
        }
        if (!EnvironmentRequirements::hasPhpExtension('xmlwriter')) {
            throw new RuntimeException('Extension "xmlwriter" is required');
        }
        if (!EnvironmentRequirements::hasPhpExtension('xmlreader')) {
            throw new RuntimeException('Extension "xmlreader" is required');
        }
        if (!EnvironmentRequirements::hasPhpExtension('iconv')) {
            throw new RuntimeException('Extension "iconv" is required');
        }
        if (!EnvironmentRequirements::hasPhpExtension('mbstring')) {
            throw new RuntimeException('Extension "mbstring" is required');
        }
        if (!EnvironmentRequirements::hasPhpExtension('hash')) {
            throw new RuntimeException('Extension "hash" is required');
        }
    }

    /**
     * Initialize.
     *
     * @param string $serviceId
     * @param string $hashingSalt
     * @param string $mode
     * @param string $hashingAlgorithm
     * @param string $hashingSeparator
     *
     * @throws RuntimeException
     * @api
     *
     */
    public function __construct(
        $serviceId,
        $hashingSalt,
        $mode = self::MODE_SANDBOX,
        $hashingAlgorithm = self::HASH_SHA256,
        $hashingSeparator = '|'
    ) {
        $this->checkPhpEnvironment();

        if ($mode !== self::MODE_LIVE && $mode !== self::MODE_SANDBOX) {
            throw new RuntimeException(sprintf('Not supported mode "%s"', $mode));
        }
        if (!array_key_exists($hashingAlgorithm, $this->hashingAlgorithmSupported)) {
            throw new RuntimeException(sprintf('Not supported hashingAlgorithm "%s"', $hashingAlgorithm));
        }
        if (empty($serviceId) || !is_numeric($serviceId)) {
            throw new RuntimeException(
                sprintf(
                    'Not supported serviceId "%s" - must be integer, %s given',
                    $serviceId,
                    gettype($serviceId)
                )
            );
        }
        if (empty($hashingSalt) || !is_string($hashingSalt)) {
            throw new RuntimeException(
                sprintf(
                    'Not supported hashingSalt "%s" - must be string, %s given',
                    $hashingSalt,
                    gettype($hashingSalt)
                )
            );
        }

        self::$mode             = $mode;
        self::$hashingAlgorithm = $hashingAlgorithm;
        self::$serviceId        = $serviceId;
        self::$hashingSalt      = $hashingSalt;
        self::$hashingSeparator = $hashingSeparator;
        self::$httpClient       = new HttpClient();

        $logger = new MonologLogger(self::LOGGER_NAME);
        $logger->pushHandler(new RotatingFileHandler(self::LOG_PATH, self::MAX_LOG_FILES, MonologLogger::INFO));

        Logger::setLogger($logger);
    }

    /**
     * @return string
     */
    public static function getItnInXml()
    {
        if (empty($_POST['transactions'])) {
            Logger::log(
                Logger::INFO,
                sprintf('No "transactions" field in POST data'),
                [
                    '_POST' => $_POST,
                ]
            );

            return '';
        }

        $transactionXml  = $_POST['transactions'];
        $transactionData = (string)base64_decode($transactionXml, true);

        return XMLParser::parse($transactionData);
    }

    /**
     * Process ITN requests.
     *
     * @return ItnIn|null|void
     * @throws Exception
     * @api
     */
    public static function doItnIn()
    {
        $transactionXml = self::getItnInXml();

        Logger::log(
            Logger::DEBUG,
            sprintf('Got "transactions" field in POST data'),
            [
                'data-raw' => $_POST['transactions'],
                'data-xml' => $transactionXml,
            ]
        );

        return ITN\Transformer::toModel($transactionXml);
    }

    /**
     * Returns response for ITN IN request.
     *
     * @param ItnIn $transaction
     * @param bool  $transactionConfirmed
     *
     * @return string
     * @api
     *
     */
    public function doItnInResponse(ItnIn $transaction, $transactionConfirmed = true)
    {
        $transaction->validate();

        $transactionHash    = self::generateHash(ITN\Transformer::modelToArray($transaction));
        $confirmationStatus = self::STATUS_NOT_CONFIRMED;

        if ($transactionHash === $transaction->getHash()) {
            $confirmationStatus = self::STATUS_CONFIRMED;
        } else {
            Logger::log(Logger::DEBUG,
                'Wygenerowany z danych zamówienia hash: '. $transactionHash.
                ' nie jest równy hashowi pobranemu ze zbudowanego ITN: '. $transaction->getHash());
        }
        if (!$transactionConfirmed) {
            $confirmationStatus = self::STATUS_NOT_CONFIRMED;
        }

        $confirmationList = [
            'serviceID'    => self::$serviceId,
            'orderID'      => $transaction->getOrderId(),
            'confirmation' => $confirmationStatus,
        ];

        $confirmationList['hash'] = self::generateHash($confirmationList);

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('confirmationList');
        $xml->writeElement('serviceID', $confirmationList['serviceID']);
        $xml->startElement('transactionsConfirmations');
        $xml->startElement('transactionConfirmed');
        $xml->writeElement('orderID', $confirmationList['orderID']);
        $xml->writeElement('confirmation', $confirmationList['confirmation']);
        $xml->endElement();
        $xml->endElement();
        $xml->writeElement('hash', $confirmationList['hash']);
        $xml->endElement();

        return $xml->outputMemory();
    }

    /**
     * Perform transaction in background.
     *
     * @param TransactionStandard $transaction
     *
     * @return TransactionBackground|string
     * @api
     */
    public function doTransactionBackground(TransactionStandard $transaction)
    {
        $data = $transaction->toArray();

        unset($data['Hash']);

        $transaction->setServiceId(self::$serviceId);
        $transaction->setHash(self::generateHash($data));
        $transaction->validate();
        $transactionData = $transaction->toArray();

        $responseObject = self::$httpClient->post(
            self::getActionUrl(self::PAYMENT_ACTON_PAYMENT),
            ['BmHeader' => 'pay-bm'],
            $transactionData
        );

        Logger::log(Logger::DEBUG, 'Sending transaction data', ['transactionData' => $transactionData]);
        $this->response = (string)$responseObject->getBody();

        return $this->parseResponse();
    }

    /**
     * Perform standard transaction.
     *
     *
     * @param TransactionStandard $transaction
     *
     * @return string
     * @api
     */
    public function doTransactionStandard(TransactionStandard $transaction)
    {
        $transaction->setServiceId(self::$serviceId);
        $transaction->setHash(self::generateHash($transaction->toArray()));
        $transaction->validate();

        return $transaction->getHtmlForm();
    }

    /**
     * Initialize transaction and get url to continue.
     *
     * @param TransactionInit $transaction
     *
     * @return SimpleXMLElement
     * @api
     */
    public function doInitTransaction(TransactionInit $transaction)
    {
        $data = $transaction->toArray();

        unset($data['Hash']);

        $transaction->setServiceId(self::$serviceId);
        $transaction->setHash(self::generateHash($data));
        $transaction->validate();
        $transactionData = $transaction->toArray();

        $responseObject = self::$httpClient->post(
            self::getActionUrl(self::PAYMENT_ACTON_PAYMENT),
            ['BmHeader' => 'pay-bm-continue-transaction-url'],
            $transactionData
        );

        Logger::log(Logger::DEBUG, 'Sending transaction data', ['transactionData' => $transactionData]);
        $this->response = (string)$responseObject->getBody();

        return $this->parseInitTransferResponse();
    }

    /**
     * Maps payment mode to service payment domain.
     *
     * @param string $mode
     *
     * @return string
     * @api
     *
     */
    final public static function mapModeToDomain($mode)
    {
        if ($mode === self::MODE_LIVE) {
            return self::PAYMENT_DOMAIN_LIVE;
        }

        return self::PAYMENT_DOMAIN_SANDBOX;
    }

    /**
     * Maps payment mode to service payment URL.
     *
     * @param string $mode
     *
     * @return string
     * @api
     *
     */
    final public static function mapModeToUrl($mode)
    {
        $domain = self::mapModeToDomain($mode);

        return sprintf('https://%s', $domain);
    }

    /**
     * Returns payment service action URL.
     *
     * @param string $action
     *
     * @return string
     * @api
     *
     */
    final public static function getActionUrl($action)
    {
        $domain = self::mapModeToDomain(self::$mode);

        switch ($action) {
            case self::PAYMENT_ACTON_PAYMENT:
            case self::PAYMENT_ACTON_PAYWAY_LIST:
            case self::GET_MERCHANT_INFO:
            case self::GET_REGULATIONS:
                break;

            default:
                $message = sprintf('Requested action "%s" not supported', $action);
                Logger::log(Logger::EMERGENCY, $message);
                throw new RuntimeException($message);
                break;
        }

        return sprintf('https://%s%s', $domain, $action);
    }

    /**
     * Generates hash.
     *
     * @param array $data
     *
     * @return string
     */
    final public static function generateHash(array $data)
    {
        $result = '';

        foreach ($data as $name => $value) {
            if (empty($value) || mb_strtolower($name) === 'hash') {
                unset($data[$name]);
                continue;
            }
            if (is_array($value)) {
                $value = array_filter($value, 'mb_strlen');
                $value = implode(self::$hashingSeparator, $value);
            }
            if (!empty($value)) {
                $result .= $value.self::$hashingSeparator;
            }
        }

        $result .= self::$hashingSalt;

        return hash(self::$hashingAlgorithm, $result);
    }

    /**
     * Returns payway list.
     *
     * @return PaywayList
     * @throws RuntimeException
     * @api
     */
    final public function doPaywayList()
    {
        $fields         = [
            'ServiceID' => self::$serviceId,
            'MessageID' => $this->generateMessageId(),
        ];
        $fields['Hash'] = self::generateHash($fields);

        $responseObject = self::$httpClient->post(
            self::getActionUrl(self::PAYMENT_ACTON_PAYWAY_LIST),
            [],
            $fields
        );

        $this->response = (string)$responseObject->getBody();
        $this->isErrorResponse();

        $responseParsed = XMLParser::parse($this->response);

        $model = Transformer::toModel($responseParsed);
        $model->validate((int)$fields['ServiceID'], (string)$fields['MessageID']);

        return $model;
    }

    /**
     * Returns payment regulations.
     *
     * @return mixed|null
     */
    final public function doPaymentRegulations()
    {
        $fields = [
            'ServiceID' => self::$serviceId,
            'MessageID' => $this->generateMessageId(),
        ];

        $fields['Hash'] = self::generateHash($fields);

        $responseObject = self::$httpClient->post(
            self::getActionUrl(self::GET_REGULATIONS),
            [],
            $fields
        );

        $this->response = (string)$responseObject->getBody();
        $this->isErrorResponse();

        $responseParsed = XMLParser::parse($this->response);
        return json_decode(json_encode((array) $responseParsed), true);
    }

    /**
     * Generates unique MessageId.
     *
     * @return string
     */
    public function generateMessageId()
    {
        return md5(time());
    }
}
