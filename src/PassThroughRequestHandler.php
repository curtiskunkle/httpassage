<?php 

namespace QuickeRouter;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class PassThroughRequestHandler implements RequestHandler {

	protected $request = null;

	protected $response = null;

	public function setResponse(Response $response) {
		$this->response = $response;
	}

	public function setRequest(Request $resquest) {
		$this->request = $resquest;
	}

	public function getResponse() {
		return $this->response;
	}

	public function getRequest() {
		return $this->request;
	}

	public function handle(Request $request): Response {
		$this->request = $request;
		return $this->response;
	}
}