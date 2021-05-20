<?php

namespace BlueMedia\OnlinePayments\Util;

use Exception;
use RuntimeException;
use SimpleXMLElement;

/**
 * XMLParser.
 *
 * @package BlueMedia\OnlinePayments\Util
 */
class XMLParser
{

    /**
     * Parses XML response.
     *
     * @param string $xml
     *
     * @return SimpleXMLElement
     */
    public static function parse($xml)
    {
        try {
            return new SimpleXMLElement($xml);
        } catch (Exception $exception) {
            Logger::log(
                Logger::ERROR,
                $exception->getMessage(),
                ['exception' => $exception, 'xml' => $xml]
            );
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
