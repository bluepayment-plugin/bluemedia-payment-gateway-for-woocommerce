<?php

final class Logger
{
    private $logger;
    private $id;

    public function __construct($id)
    {
        $this->logger = new WC_Logger();
        $this->id = $id;
    }

    public function log($message)
    {
        $hash = $this->generateHash();
        $this->logger->add($this->id, sprintf('%s %s', $hash, $message));

        return $hash;
    }

    private function generateHash()
    {
        return md5($this->id.time());
    }
}
