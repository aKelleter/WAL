<?php
const ROUTES = [
	'/' => [
		'controller' => App\Controller\HomeController::class,
		'method' => 'home'
	],	
	'/home' => [
		'controller' => App\Controller\HomeController::class,
		'method' => 'home'
	],
	'/contact' => [
		'controller' => App\Controller\HomeController::class,
		'method' => 'contact'
	]
];