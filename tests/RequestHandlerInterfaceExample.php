<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RequestHandlerInterfaceExample implements RequestHandler {
	public function handle(Request $request): Response {
		$response = Provider::getResponse();
		$response->getBody()->write("Applied request handler");
		return $response;
	}
}