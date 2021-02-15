<?php 
namespace QuickRouter\Middleware;
/**
 * Subrouter middleware
 */
class SubRouter  {
	public function __construct(\QuickRouter\Router $router) {
		return function($context) use ($router) {
			

			return $router->route($context);
		};
	}
}