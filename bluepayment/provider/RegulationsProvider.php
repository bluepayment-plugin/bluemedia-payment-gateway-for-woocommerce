<?php

final class RegulationsProvider
{
    private $bluemedia_sdk_handler;

    public function __construct(BlueMediaSdkHandler $bluemedia_sdk_handler)
    {
        $this->bluemedia_sdk_handler = $bluemedia_sdk_handler;
    }

    public function getRegulations($channels_list)
    {
        $regulations_get = $this->bluemedia_sdk_handler->call(RegulationsGetHandler::class);

        return $this->getDefinedRegulationsForGateways($regulations_get, $channels_list);
    }

    private function getDefinedRegulationsForGateways($regulations_get, $channels_list)
    {
        $regulation_list = $this->getRegulationList($regulations_get);

        if (!empty($regulation_list)) {
            foreach ($channels_list as $channel) {
                foreach ($regulation_list as $regulation) {
                    if ($this->hasGatewayRegulation($regulation, $channel)) {
                        $channels_list[$channel['gatewayID']]['regulationID'] = empty($regulation['regulationID']) ? '' : $regulation['regulationID'];
                        $channels_list[$channel['gatewayID']]['regulationUrl'] = empty($regulation['url']) ? '' : $regulation['url'];
                        $channels_list[$channel['gatewayID']]['regulationType'] = empty($regulation['type']) ? '' : $regulation['type'];
                        $channels_list[$channel['gatewayID']]['regulationLanguage'] = empty($regulation['language']) ? '' : $regulation['language'];
                        $channels_list[$channel['gatewayID']]['regulationInputLabel'] = empty($regulation['inputLabel']) ? '' : $regulation['inputLabel'];
                    }
                }
            }
        }

        return $channels_list;
    }

    private function getRegulationList($regulations_get)
    {
        $regulation_list = [];

        if (!empty($regulations_get['regulations']['regulation'])) {
            foreach ($regulations_get['regulations']['regulation'] as $regulation) {
                $regulation_list[] = $regulation;
            }
        }

        return $regulation_list;
    }

    private function hasGatewayRegulation($regulation, $channel)
    {
        return isset($regulation['gatewayID']) && ($channel['gatewayID'] == $regulation['gatewayID']);
    }
}
