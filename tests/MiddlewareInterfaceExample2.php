<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MiddlewareInterfaceExample2 implements Middleware {
	public function process(Request $request, RequestHandler $handler): Response {
		$response = $handler->handle($request);
		$response->getBody()->write("middleware applied");
		return $response->withHeader("x-some-header", "value");
	}
}