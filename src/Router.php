<?php 
namespace QuickRouter;

/**
 * Wrapper class for altorouter
 */
class Router extends \AltoRouter{

	// public function __construct($routes = [], $basePath = '', $matchTypes = []) {
	// 	parent::__construct($routes, $basePath, $matchTypes);
	// }

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
	 * And we would like to assign matched routes to responses in order to make
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
	 * @param  closure $middleware 
	 */
	public function useMiddleware($middleware) {
		$this->middleware[] = $middleware;
		return $this;
	}

	/**
	 * Routes the current request and applies any callbacks that match
	 * @param  Request  $resquest 
	 * @param  Response $response
	 * @return bool - whether or not a match was found
	 */
	public function route(Request $request, Response $response) {

		$match = $this->match();

		if ($match) {
			$request->routeParams = $match["params"];
		}

		if (!empty($this->middleware)) {
			foreach ($this->middleware as $m) {
				$result = true;
				if (is_callable($m)) {
					$result = call_user_func_array($m, [$request, $response]); 
				}
				if ($result === false) {
					return false;
				}
			}
		}
 		
		if (!$match) {
			$response->setCode(404);
			return false;
		}

		$response->setMatchedRoute($match["name"]);
		
		if ($match["target"] instanceof Router) {
			if (!empty($this->basePath)) {
				$match["target"]->setBasePath($this->basePath);
			}
			return $match["target"]->route($request, $response);
		}

		if (is_array($match["target"])) {
			foreach ($match["target"] as $callback) {
				call_user_func_array($callback, [$request, $response]);
			}
		} elseif(is_callable($match['target']) ) {
			call_user_func_array($match['target'], [$request, $response]); 
		} else {
			$response->setCode(500);
		}
		return true;
	}
}