<?php
return [
	'profile' => [
		'oj-name' => 'Morithm Online Judge',
		'oj-name-short' => 'MOJ',
		'administrator' => 'root',
		'admin-email' => 'admin@morithm.com',
		'QQ-group' => '',
		'ICP-license' => ''
	],
	'database' => [
		'database' => 'app_moj',
		'username' => 'root',
		'password' => '_database_password_',
		'host' => '127.0.0.1'
	],
	'web' => [
		'domain' => null,
		'main' => [
			'protocol' => 'http',
			'host' => '_httpHost_',
			'port' => 80
		],
		'blog' => [
			'protocol' => 'http',
			'host' => '_httpHost_',
			'port' => 80
		]
	],
	'security' => [
		'user' => [
			'client_salt' => 'salt0'
		],
		'cookie' => [
			'checksum_salt' => ['salt1', 'salt2', 'salt3']
		],
	],
	'mail' => [
		'noreply' => [
			'username' => 'noreply@morithm.com',
			'password' => '_mail_noreply_password_',
			'host' => 'smtp.morithm.com',
			'secure' => 'tls',
			'port' => 587
		]
	],
	'judger' => [
		'socket' => [
			'port' => '233',
			'password' => '_judger_socket_password_'
		]
	],
	'switch' => [
		'web-analytics' => false,
		'blog-domain-mode' => 3
	]
];
