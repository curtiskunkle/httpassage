<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MiddlewareInterfaceExample1 implements Middleware {
	public function process(Request $request, RequestHandler $handler): Response {
		$request = $request->withAttribute("test","test");
		return $handler->handle($request);
	}
}