<?php 

namespace QuickRouter;

class Factory {

	public static function createContext($request, $response, $state = [], $params = [], $matchedRoutes = [], $flag = false) {
		return new Context($request, $response, $state, $params, $matchedRoutes, $flag);
	}

	public static function createRouter() {
		return new Router();
	}

}
