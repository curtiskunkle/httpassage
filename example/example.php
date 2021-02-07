<?php

require_once(__DIR__ . "/../vendor/autoload.php");

$req = new \GuzzleHttp\Psr7\ServerRequest("GET", "http://example.com");
$res = new \GuzzleHttp\Psr7\Response();
$context = \QuickRouter\Factory::createContext($req, $res);

$router = \QuickRouter\Factory::createRouter();
$router->useMiddleware([
	function($context) {
		$res = $context->getResponse();
		$res->getBody()->write("middleware 1");
		return $context->withResponse($res);
	},
	function($context) {
		$res = $context->getResponse();
		$res->getBody()->write("middleware 2");
		return $context->withResponse($res);
	},
]);
$router->get("/quick-router/[a:action]/path", [
	function($context) {
		$res = $context->getResponse();
		$res->getBody()->write("test");
		return $context->withResponse($res);
	},
	function($context) {
		$res = $context->getResponse();
		$res->getBody()->write(" test again");
		return $context->withResponse($res);
	},
]
);

$context = $router->route($context);

echo "<pre>";
print_r($context->getResponse()->getBody()->__toString());
echo "</pre>";die;