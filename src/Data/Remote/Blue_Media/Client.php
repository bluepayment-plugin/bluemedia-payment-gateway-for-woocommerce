<?php

namespace Ilabs\BM_Woocommerce\Data\Remote\Blue_Media;

use Exception;
use \Isolated\Blue_Media\Isolated_Guzzlehttp\GuzzleHttp\Client as GuzzleHttpClient;

class Client {


	function continue_transaction_request(
		array $data,
		string $gateway_url
	) {

		$client = new GuzzleHttpClient();

		try {
			$response = $client->post( $gateway_url,
				[
					'headers'     => [
						'BmHeader' => 'pay-bm-continue-transaction-url',
					],
					'form_params' => $data,
					'verify'      => true,
				] );

			//$statusCode   = $response->getStatusCode();
			return $response->getBody()->getContents();
		} catch ( Exception $e ) {
			return "Error: " . $e->getMessage();
		}
	}
}