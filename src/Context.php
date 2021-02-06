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
	protected $routeParameters = [];

	/**
	 * General purpose context meta data field
	 * @var array
	 */
	protected $state = null;

	/**
	 * Flag that indicates the context should no longer be routed
	 * @var boolean
	 */
	protected $exitRouting = false;

	public function __construct(
		\Psr\Http\Message\ServerRequestInterface $request, 
		\Psr\Http\Message\ResponseInterface $response, 
		$state = null,
		array $routeParameters = []
	) {
		$this->request = $request;
		$this->response = $response;
		$this->state = $state;
		$this->routeParameters = $routeParameters;
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
			$this->routeParameters
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
			$this->routeParameters
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
			$this->routeParameters
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
			$params
		);
	}

	/**
	 * Flags this context to exit routing
	 * @return Context
	 */
	public function _exit() {
		$this->exit = true;
	}
}