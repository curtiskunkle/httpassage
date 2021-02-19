<?php 

namespace HTTPassage\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * HTTPassage uses this handler when processing PSR15 middleware.
 *
 * In order to capture changes to requests and responses when using PSR15 middleware,
 * we set the original request and response on this handler before passing it to the middleware.
 * Then, if a new request is passed to this handler, it captures the new request so that we can 
 * assign it to context afterwards. We capture the new response by simply capturing what the 
 * middleware::process function returns.  Probably not the intended pattern, but this allows us to
 * apply middleware one at a time and intermingle it with other supported callback handlers in a simple list.
 *
 * @example 
 * $router->map("GET","/some/path/?", [
 *
 * 		//apply a psr 15 middleware
 * 		new Psr15MiddlewareClass1(),
 *
 * 		//apply an arbitrary function of a class
 * 		"SomeClass::someCallableFunction"
 *
 * 		//apply another psr 15 middleware
 * 		new Psr15MiddlewareClass2(),
 *
 * 		//apply an anonymous function
 * 		function($context) {
 * 			//...do stuff with context
 * 			return $context
 * 		},
 *
 * 		//apply a psr 15 request handler
 * 		new Psr15RequestHandlerClass()
 * ]);
 */
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