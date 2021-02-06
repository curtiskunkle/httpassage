<?php 

namespace QuickRouter;

class Factory {

	public static function createContext($request, $response, $state = [], $params = []) {
		return new Context($request, $response, $state, $params);
	}

	public static function createRouter() {
		return new Router();
	}

}
