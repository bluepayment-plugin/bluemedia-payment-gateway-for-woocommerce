<?php

final class ServiceIdFieldValidator implements ValidatorInterface
{
    private $errors;

    public function validate()
    {
        $service_id_fields_name = 'woocommerce_bluemedia_payment_gateway_service_id_';

        foreach (CurrencyEnum::getEnumConstants() as $suffix) {
            if (!empty($_POST[$service_id_fields_name . $suffix])) {
                if (!preg_match('/^\d+$/', $_POST[$service_id_fields_name . $suffix])) {
                    $this->addError(
                        'error',
                        $service_id_fields_name . $suffix,
                        'ServiceID ['.$suffix.'] must be digits only!'
                    );
                }
            }
        }
    }

    private function addError($type, $field, $message) {
        $this->errors[] = [$type, $field, $message];
    }

    public function getErrors() {
        return $this->errors;
    }
}