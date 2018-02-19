<?php
/**
 * Created by PhpStorm.
 * User: tkapusta
 * Date: 06.12.2017
 * Time: 21:27
 */

class WC_Bluepayment_gateway
{
    const FAILED_CONNECTION_RETRY_COUNT = 5;
    const MESSAGE_ID_STRING_LENGTH = 32;

    public $blue_media_settings = [];

    public function __construct($blue_media_settings)
    {
        $this->blue_media_settings = $blue_media_settings;
    }

    public function syncGateways()
    {
        $result = array();
        $hashMethod = 'sha256';
        $gatewayListAPIUrl = $this->getGatewayListUrl();
        $serviceId = $this->blue_media_settings['service_id'];
        $messageId = $this->randomString(self::MESSAGE_ID_STRING_LENGTH);
        $hashKey = $this->blue_media_settings['hash_key'];
        $tryCount = 0;
        $loadResult = false;
        while (!$loadResult) {
            $loadResult = $this->loadGatewaysFromAPI($hashMethod, $serviceId, $messageId, $hashKey, $gatewayListAPIUrl);
            if ($loadResult) {
                $result['success'] = $this->saveGateways((array)$loadResult);
                break;
            } else {
                if ($tryCount >= self::FAILED_CONNECTION_RETRY_COUNT) {
                    $result['error'] = 'Exceeded the limit of attempts to sync gateways list!';
                    break;
                }
            }
            $tryCount++;
        }
    }

    private function loadGatewaysFromAPI($hashMethod, $serviceId, $messageId, $hashKey, $gatewayListAPIUrl)
    {
        $hash = hash($hashMethod, $serviceId . '|' . $messageId . '|' . $hashKey);
        $data = array(
            'ServiceID' => $serviceId,
            'MessageID' => $messageId,
            'Hash' => $hash
        );
        $fields = (is_array($data)) ? http_build_query($data) : $data;
        try {
            $curl = curl_init($gatewayListAPIUrl);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $curlResponse = curl_exec($curl);
            curl_close($curl);
            if ($curlResponse == 'ERROR') {
                return false;
            } else {
                $response = simplexml_load_string($curlResponse);
                return $response;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    private function saveGateways($gatewayList)
    {

        global $wpdb;
        // if you have followed my suggestion to name your table using wordpress prefix
        $table_name = $wpdb->prefix . 'blue_gateways';

        $inserted_ids = [];
        if (isset($gatewayList['gateway'])) {
            foreach ($gatewayList['gateway'] as $gatewayXMLObject) {
                $gateway = (array)$gatewayXMLObject;
                if (isset($gateway['gatewayID']) && isset($gateway['gatewayName']) && isset($gateway['gatewayType']) && isset($gateway['bankName']) && isset($gateway['iconURL']) && isset($gateway['statusDate'])) {
                    $data = array(
                        'gateway_id' => $gateway['gatewayID'],
                        'bank_name' => $gateway['bankName'],
                        'gateway_type' => $gateway['gatewayName'],
                        'gateway_name' => $gateway['gatewayType'],
                        'gateway_logo_url' => $gateway['iconURL'],
                        'status_date' => $gateway['statusDate'],
                        'mode' => $this->blue_media_settings['mode']
                    );

                    $gateway_id = esc_sql($data['gateway_id']);
                    $mode = esc_sql($this->blue_media_settings['mode']);

                    $gateway_db = $wpdb->get_results(
                        "SELECT * FROM $table_name WHERE gateway_id = '$gateway_id' AND mode ='$mode'"
                    );

                    if ($gateway_db){
                        $inserted_ids[] = $gateway_db[0]->entity_id;
                        $wpdb->update($table_name,
                            $data,
                            array('entity_id' => $gateway_db[0]->entity_id));
                    } else {
                        $wpdb->insert($table_name, $data, '%s');
                        $inserted_ids[] = $wpdb->insert_id;
                    }
                    $wpdb->query("DELETE FROM $table_name WHERE entity_id not in (".implode(', ', $inserted_ids) .")");

                }
            }
        }
    }

    public function getSimpleGatewaysList()
    {
        global $wpdb;
        // if you have followed my suggestion to name your table using wordpress prefix
        $table_name = $wpdb->prefix . 'blue_gateways';

        $gateway_db = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE mode ='{$this->blue_media_settings['mode']}' order by gateway_sort_order",
            ARRAY_A
        );
        return $gateway_db;
    }

    public function randomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    private function getGatewayListUrl()
    {
        if ($this->blue_media_settings['mode'] == 'sandbox') {
            return 'https://pay-accept.bm.pl/paywayList';
        }
        return 'https://pay.bm.pl/paywayList';
    }
}