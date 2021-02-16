<?php 

namespace QuickRouter;

class Context {

	/**
	 * A PSR7 ServerRequest
	 */
	protected $request;

	/**
	 * A PSR7 Response
	 */
	protected $response;

	/**
	 * Named parameters extracted from matched route
	 * @var array
	 */
	protected $routeParameters;

	/**
	 * General purpose context meta data field
	 * @var array
	 */
	protected $state = null;

	/**
	 * List of matched routes set by Router
	 * @var array
	 */
	protected $matchedRoutes;

	/**
	 * Flag that indicates the context should no longer be routed
	 * @var boolean
	 */
	protected $exitRouting = false;

	public function __construct(
		\Psr\Http\Message\ServerRequestInterface $request, 
		\Psr\Http\Message\ResponseInterface $response, 
		$state = null,
		array $routeParameters = [],
		array $matchedRoutes = [],
		$exitRouting = false
	) {
		$this->request = $request;
		$this->response = $response;
		$this->state = $state;
		$this->routeParameters = $routeParameters;
		$this->matchedRoutes = $matchedRoutes;
		$this->exitRouting = $exitRouting ? true : false;
	}

	/**
	 * Getters
	 */
	public function getRequest() {
		return $this->request;
	}
	public function getResponse() {
		return $this->response;
	}
	public function getState() {
		return $this->state;
	}
	public function getRouteParameters() {
		return $this->routeParameters;
	}
	public function getRouteParameter($key, $default = null) {
		return !empty($this->routeParameters[$key]) ? $this->routeParameters[$key] : $default;
	}
	public function getMatchedRoutes() {
		return $this->matchedRoutes;
	}
	public function shouldExitRouting() {
		return $this->exitRouting;
	}

	/**
	 * Update the context request
	 * @param  \Psr\Http\Message\ServerRequestInterface $request
	 * @return Context with provided request
	 */
	public function withRequest(\Psr\Http\Message\ServerRequestInterface $request) {
		return new Context(
			$request,
			$this->response,
			$this->state,
			$this->routeParameters,
			$this->matchedRoutes,
			$this->exitRouting
		);
	}

	/**
	 * Update the context response
	 * @param  \Psr\Http\Message\ResponseInterface $response
	 * @return Context with provided response
	 */
	public function withResponse(\Psr\Http\Message\ResponseInterface $response) {
		return new Context(
			$this->request,
			$response,
			$this->state,
			$this->routeParameters,
			$this->matchedRoutes,
			$this->exitRouting
		);
	}

	/**
	 * Update the context state
	 * @param  $state 
	 * @return Context with provided state
	 */
	public function withState($state) {
		return new Context(
			$this->request,
			$this->response,
			$state,
			$this->routeParameters,
			$this->matchedRoutes,
			$this->exitRouting
		);
	}

	/**
	 * Update the context route parameters
	 * @param  $state 
	 * @return Context with provided route parameters
	 */
	public function withRouteParameters($params) {
		return new Context(
			$this->request,
			$this->response,
			$this->state,
			$params,
			$this->matchedRoutes,
			$this->exitRouting
		);
	}

	/**
	 * Update the context matched routes
	 * @param  $state 
	 * @return Context with provided matched routes
	 */
	public function withMatchedRoutes($matchedRoutes) {
		return new Context(
			$this->request,
			$this->response,
			$this->state,
			$this->routeParameters,
			$matchedRoutes,
			$this->exitRouting
		);
	}

	/**
	 * Flag context to exit routing
	 * @param  $flag
	 * @return Context
	 */
	public function withExitFlag($flag = true) {
		$flag = $flag ? true : false;
		return new Context(
			$this->request,
			$this->response,
			$this->state,
			$this->routeParameters,
			$this->matchedRoutes,
			$flag
		); 
	}
}