<?php

require_once 'PaymentChannelInterface.php';
require_once 'BlikPBLPayment.php';
require_once 'CardPayment.php';
require_once 'InstallmentPayment.php';
require_once 'BackgroundPayment.php';
require_once 'BackgroundSessionPayment.php';
require_once 'SmartneyPayment.php';

final class BackgroundPaymentChannels
{
    private $backgroundPaymentChannel = [];

    public function addPaymentMethod(PaymentChannelInterface $backgroundPaymentChannel)
    {
        $this->backgroundPaymentChannel[] = $backgroundPaymentChannel;
    }

    public function handle()
    {
        foreach ($this->backgroundPaymentChannel as $channel) {
            /** @var $paymentMethod PaymentChannelInterface */
            if ($channel->canProcess()) {
                 return $channel->process();
            }
        }

        return 0;
    }
}
