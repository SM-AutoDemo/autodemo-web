<?php
if(!defined('_settings_included'))
{
	header('HTTP/1.0 403 Forbidden');
	exit;
}

return
[
	'127.0.0.1:27015' =>
	[
		'api_key' => 'Privet_Wend4r',
		'demo_life_time' => 259200,

		'ftp' =>
		[
			'host' => '127.0.0.1',
			'user' => '',
			'password' => '',
			'demo_path' => 'addons/sourcemod/data/demos/'
		],
	],
	'127.0.0.1:27016' =>
	[
		'api_key' => 'Privet_Wend4r',
		'demo_life_time' => 259200,

		'ftp' =>
		[
			'host' => '127.0.0.1',
			'user' => '',
			'password' => '',
			'demo_path' => 'addons/sourcemod/data/demos/'
		],
	],
];