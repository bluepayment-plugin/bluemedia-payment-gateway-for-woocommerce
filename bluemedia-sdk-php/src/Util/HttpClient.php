<?php

namespace BlueMedia\OnlinePayments\Util;

use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    /** @var GuzzleHttp\Client */
    private $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new GuzzleHttp\Client(
            [
                GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => false,
                GuzzleHttp\RequestOptions::HTTP_ERRORS     => false,
                GuzzleHttp\RequestOptions::VERIFY          => true,
                'exceptions'                               => false,
            ]
        );
    }

    /**
     * Perform POST request.
     *
     * @param string $url
     * @param array  $headers
     * @param null   $data
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function post($url, array $headers = [], $data = null, array $options = [])
    {
        $options = array_merge(
            $options,
            [
                GuzzleHttp\RequestOptions::HEADERS => $headers,
                GuzzleHttp\RequestOptions::FORM_PARAMS    => $data,
            ]
        );

        return $this->guzzleClient->post($url, $options);
    }
}
