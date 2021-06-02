<?php

interface PaymentChannelInterface
{
    public function canProcess();
    public function process();
}
