<?php 

namespace QuickRouter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface ContextInterface {

	public function __construct(
		Request $request, 
		Response $response, 
		$state,
		array $routeParameters,
		array $matchedRoutes,
		$exitRouting
	);

	//getters
	public function getRequest(): Request;
	public function getResponse(): Response;
	public function getState();
	public function getRouteParameters();
	public function getRouteParameter($key, $default);
	public function getMatchedRoutes();
	public function shouldExitRouting(): bool;

	//setters
	public function withRequest(Request $request): ContextInterface;
	public function withResponse(Response $response): ContextInterface;
	public function withState($state): ContextInterface;
	public function withRouteParameters($params): ContextInterface;
	public function withMatchedRoutes($matchedRoutes): ContextInterface;
	public function withExitFlag($flag ): ContextInterface;
}