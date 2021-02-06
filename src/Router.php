<?php 
namespace QuickRouter;

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

		//look for a route match
		$match = $this->match($context->getRequest()->getUri()->getPath(), $context->getRequest()->getMethod());

		if ($match) {
			//set route params if they exist
			$context = $context->withRouteParameters($match["params"]);
		}

		if (!empty($this->middleware)) {
			foreach ($this->middleware as $m) {
				if (is_callable($m)) {
					$context = call_user_func_array($m, [$context]); 
				}
				if (!($context instanceof Context)) {
					throw new \RuntimeException("Middleware must return instance of \QuickRouter\Context");
				}
			}
		}
 		
		if (!$match) {
			return $context->withResponse($context->getResponse()->withStatus(404));
		}

		$response->setMatchedRoute($match["name"]);
		
		if ($match["target"] instanceof Router) {
			if (!empty($this->basePath)) {
				$match["target"]->setBasePath($this->basePath);
			}
			return $match["target"]->route($context);
		}

		//@todo handle here if router in array of callbacks
		if (is_array($match["target"])) {
			foreach ($match["target"] as $callback) {
				$context = call_user_func_array($callback, [$context]);
			}
		} elseif(is_callable($match['target']) ) {
			$context = call_user_func_array($match['target'], [$context]); 
		} 

		return $context;
	}
}