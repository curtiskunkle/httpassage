<?php 
namespace QuickRouter;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Wraps altorouter, adds some additional functionality
 */
class Router extends \AltoRouter{

	/**
	 * Array of callables to be applied to a request before a matched route callback
	 * @var array
	 */
	public $middleware = [];

	/**
	 * Add route getter since AltoRouter does not provide one
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * Override map method of AltoRouter so Router can automatically
	 * name routes after the route string
	 * 
	 * This is all because AltoRouter does not return the matched route string
	 * And we would like to assign matched routes to context in order to make
	 * it easier to determine exactly how a request was routed
	 */
	public function map($method, $route, $target, $name = null) {
		parent::map($method, $route, $target, "$method-$route");
	}

	/**
	 * Shorthand for mapping routes to common HTTP methods
	 */
	public function get($route, $target) {$this->map("GET", $route, $target);}
	public function post($route, $target) {$this->map("POST", $route, $target);}
	public function put($route, $target) {$this->map("PUT", $route, $target);}
	public function delete($route, $target) {$this->map("DELETE", $route, $target);}
	public function patch($route, $target) {$this->map("PATCH", $route, $target);}
	public function options($route, $target) {$this->map("OPTIONS", $route, $target);}

	/**
	 * Register middleware
	 * @param  callable $middleware 
	 */
	public function useMiddleware($middleware) {
		$this->middleware[] = $middleware;
		return $this;
	}

	/**
	 * Routes the current context and applies any callbacks that match
	 * @param  Context $context
	 * @return Context
	 */
	public function route(Context $context) {

		if (empty($context->getRequest()->getAttribute("matchedRoutes"))) {
			$context = $context->withRequest($context->getRequest()->withAttribute("matchedRoutes", []));
		}

		//look for a route match
		$match = $this->match($context->getRequest()->getUri()->getPath(), $context->getRequest()->getMethod());

		if ($match) $context = $context->withRouteParameters(array_merge(
			$context->getRouteParameters(),
			$match["params"]
		));

		if (!empty($this->middleware)) {
			foreach ($this->middleware as $m) {
				$context = $this->applyCallback($context, $m);
			}
		}
 		
		if (!$match) return $context->withResponse($context->getResponse()->withStatus(404));
		
		$context = $context->withMatchedRoutes(array_merge(
			$context->getMatchedRoutes(),
			[$match["name"]]
		));
		
		$context = $this->applyCallback($context, $match["target"]);

		return $context;
	}

	/**
	 * Apply the various types of middleware context as it is routed
	 * @param  Context $context 
	 * @param  mixed   $callback
	 * @param  bool    $includeRouters if whether or not to route $context if router encountered
	 * @return Context
	 */
	protected function applyCallback($context, $callback, $handleRouters = false) {

		//array of callbacks
		if (is_array($callback)) {
			foreach ($callback as $cb) {
				$context = $this->applyCallback($context, $cb);
			}
		}

		//PSR15 RequestHandlerInterface
		if ($callback instanceof \Psr\Http\Server\RequestHandlerInterface) {
			$response = $callback->handle($context->getRequest());
			return $response instanceof \Psr\Http\Message\ResponseInterface
				? $context->withResponse($response)
				: $context;
		}

		//PSR15 MiddlewareInterface
		//@todo

		if (is_callable($callback)) {
			$result = call_user_func_array($callback, [$context]);
			if ($result instanceof Context) {
				$context = $result;
			}
		} 

		return $context;
	}
}