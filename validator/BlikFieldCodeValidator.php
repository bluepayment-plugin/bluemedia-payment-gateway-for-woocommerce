<?php

final class BlikFieldCodeValidator implements ValidatorInterface
{
    public function validate()
    {
        if (empty($_POST['bluemedia_blik_code'])) {
            wc_add_notice(__("Please insert BLIK code.", 'bluepayment-gateway-for-woocommerce'), 'error');
        }
    }
}
