<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     		=> \CodeIgniter\Filters\CSRF::class,
		'toolbar' 		=> \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' 		=> \CodeIgniter\Filters\Honeypot::class,
		'auth'	   		=> \App\Filters\Auth::class,
		'authAdmin'	   	=> \App\Filters\AuthAdmin::class,
		'checkPermission' => \App\Filters\CheckPermission::class,
		'authApi' 		=> \App\Filters\AuthApi::class
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			'auth' => [
				'except' => [
					'login/',
					'login/*',
					'cadastro/',
					'cadastro/*',
					'mensagem/',
					'mensagem/*',
					'cron/',
					'cron/*',
					'api/',
					'api/*'
				]
			],
			'checkPermission' => [
				'except' => [
					'login/',
					'login/*',
					'cadastro/',
					'cadastro/*',
					'mensagem/',
					'mensagem/*',
					'cron/',
					'cron/*',
					'api/',
					'api/*'
				]
			],
			'csrf' => [
				'except' => [
					'ajax/categoria/store',
					'ajax/usuario/storeFoto',
					'ajax/grafico/getPorCategoria',
					'api',
					'api/*'
				]
			]
		],
		'after'  => [
			'toolbar',
			'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [
		'authApi' => [
			'before' => [
				'api/usuario',
				'api/usuario/*',
				'api/lancamento',
				'api/lancamento/*',
				'api/categoria',
				'api/categoria/*',
				'api/orcamento',
				'api/orcamento/*'
			]
		],
		'authAdmin' => [
			'before' =>  [
				'admin',
				'admin/*'
			]
		]
	];
}
