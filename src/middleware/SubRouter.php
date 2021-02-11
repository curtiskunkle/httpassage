<?php 
namespace QuickRouter\Middleware;
/**
 * Default XML body parse middleware
 */
class SubRouter  {
	public function __construct(\QuickRouter\Router $router) {
		return function($context) use ($router) {
			//...
		};
	}
}