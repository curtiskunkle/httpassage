<?php

require_once(__DIR__ . "/../vendor/autoload.php");

$req = new \GuzzleHttp\Psr7\ServerRequest("GET", "http://a-host/quick-router/a/path");
$res = new \GuzzleHttp\Psr7\Response();
$context = \QuickRouter\Factory::createContext($req, $res);

$router = \QuickRouter\Factory::createRouter();
$router->get("/quick-router/[a:action]/path", function($context) {
	$res = $context->getResponse();
	return $context->withResponse(
		$res->withHeader("A header", "A value")
	);
});

$context = $router->route($context);