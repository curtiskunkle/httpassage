<?php 
namespace HTTPassage;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use HTTPassage\CallbackHandler\CallbackHandlerInterface as Callbackhandler;

/**
 * Wraps altorouter, adds some additional functionality
 */
class Router extends \AltoRouter{

	public function __construct($routes = [], $basePath = '', $matchTypes = []) {
		parent::__construct($routes, $basePath, $matchTypes);

		//set default callback handlers
		$this->useCallbackHandlers([
			new \HTTPassage\CallbackHandler\PSR15RequestHandlerCallbackHandler(),
			new \HTTPassage\CallbackHandler\PSR15MiddlewareCallbackHandler(),
			new \HTTPassage\CallbackHandler\RouterCallbackHandler(),
			new \HTTPassage\CallbackHandler\CallableCallbackHandler(),
		]);
	}

	/**
	 * Array of ballbacks to be applied to a request before a matched route callback
	 * @var array
	 */
	protected $middleware = [];

	/**
	 * Array of callback handlers
	 * @var array
	 */
	protected $callbackHandlers = [];

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
	 * Set this middleware
	 * @param  array  $middleware array of callbacks
	 */
	public function useMiddleware(array $middleware) {
		$this->middleware = $middleware;
		return $this;
	}

	/**
	 * Register middleware
	 * @param  callable $middleware 
	 */
	public function addMiddleware($middleware) {
		$this->middleware[] = $middleware;
		return $this;
	}

	public function getMiddleware() {
		return $this->middleware;
	}

	/**
	 * Set the callback handlers
	 * @param  array $handlers array of instances of classes that implement callback handler
	 */
	public function useCallbackHandlers(array $handlers) {
		if (empty($handlers)) throw new \InvalidArgumentException("Parameter 'handlers' cannot be empty");
		$this->callbackHandlers = [];
		foreach ($handlers as $handler) {
			$this->addCallbackHandler($handler);
		}
		return $this;
	}

	/**
	 * Append a handler to the callback handlers
	 * @param CallbackHandler $handler
	 */
	public function addCallbackHandler(CallbackHandler $handler) {
		$this->callbackHandlers[] = $handler;
		return $this;
	}

	public function getCallbackHandlers() {
		return $this->callbackHandlers;
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
			$context = $context->withRouteParameters(array_merge(
				$context->getRouteParameters(),
				$match["params"]
			))->withMatchedRoutes(array_merge(
				$context->getMatchedRoutes(),
				[$match["name"]]
			));	
		} 

		/**
		 * Middleware assigned to router will be applied
		 * if there is a match or not
		 */
		if (!empty($this->middleware)) {
			foreach ($this->middleware as $m) {
				$context = $this->applyCallback($context, $m);
				if ($context->shouldExitRouting()) break;
			}
		}

		//if this flag set by middleware, do not apply callbacks
		if ($context->shouldExitRouting()) return $context;
 		
 		/**
 		 * If there is a match, apply the assigned callbacks
 		 * Otherwise, just return context after setting a 404 on the response
 		 */
		return $match 
			? $this->applyCallback($context, $match["target"])
			: $context->withResponse($context->getResponse()->withStatus(404));
	}

	/**
	 * Apply the various types of middleware context as it is routed
	 * @param  Context $context 
	 * @param  mixed   $callback
	 * @return Context
	 */
	protected function applyCallback($context, $callback) {

		//array of callbacks
		if (is_array($callback)) {
			foreach ($callback as $cb) {
				$context = $this->applyCallback($context, $cb);
				if (!($context instanceof Context)) {
					throw new \RuntimeException("HTTPassage callback must return an instance of \HTTPassage\Context");
				}
				if ($context->shouldExitRouting()) {
					return $context;
				}
			}
		}

		foreach ($this->callbackHandlers as $handler) {
			if ($handler->meetsCriteria($callback)) {
				return $handler->handle($context, $callback);
			}
		}

		return $context;
	}
}