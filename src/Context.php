<?php 

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
	 * General purpose context meta data field
	 * @var array
	 */
	protected $state = [];

	/**
	 * Constructor 
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 */
	public function __construct(ServerRequestInterface $request, ResponseInterface $response) {
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * Replace the current request instance
	 * @param ServerRequestInterface $request
	 */
	public function setRequest(ServerRequestInterface $request) {
		$this->request = $request;
	}

	/**
	 * Replace the current response instance
	 * @param ResponseInterface $response
	 */
	public function setResponse(ResponseInterface $response) {
		$this->response = $response;
	}

	/**
	 * Request getter
	 * @return ServerRequestInterface
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Response getter
	 * @return ResponseInterface
	 */
	public function getResponse() {
		return $this->response;
	}

	//@todo how will these work
	public function setState() {}
	public function getState() {}

	public function useRequest($callback) {
		//@todo validate that this is a function
		$this->setRequest($callback($this->request));
	}
	public function useResponse() {}
}

//example
//todo - can this somehow be cleaner??
// $context->useRequest(function($request) {
// 	return $request->withHeader("A header");
// });