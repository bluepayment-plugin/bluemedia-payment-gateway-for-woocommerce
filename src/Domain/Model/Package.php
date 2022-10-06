<?php

namespace Ilabs\BM_Woocommerce\Domain\Model;

class Package {

	const TYPE_STRING = 1;

	const TYPE_BOOLEAN = 2;

	const TYPE_NUMERIC = 3;

	const TYPE_ENUM = 4;


	public $type = [
		'id'     => 'type',
		'name'   => 'Type',
		'type'   => self::TYPE_ENUM,
		'values' => [
			'package' => 'Paczka',
			'palette' => 'paleta',
		],
	];

	public $is_non_standard = [
		'id'   => 'is_nstd',
		'name' => 'Is non standard',
		'type' => self::TYPE_BOOLEAN,
	];

	public $width = [
		'id'   => 'width',
		'name' => 'Width',
		'type' => self::TYPE_NUMERIC,
	];
}