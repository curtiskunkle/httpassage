<?php

namespace HTTPassage\CallbackHandler;
use HTTPassage\ContextInterface as Context;

interface CallbackHandlerInterface {

	/**
	 * This function should return whether or not the call back meets the criteria for this callback handler.
	 * The callback is the callback that is mapped to a route when mapping routes to a router.
	 * In the following examples, string "a callback" is the callback
	 * 
	 * @example
	 * $router->map("GET", "/a/path", "a callback");
	 * $router->get("/a/path", "a callback");
	 * 
	 * @param  mixed $callback the callback
	 * @return bool
	 */
	public function meetsCriteria($callback): bool;

	/**
	 * This function applies the callback if meetsCriteria returned true
	 * 
	 * @param  Context $context 
	 * @param  mixed   $callback
	 * @return Context
	 */
	public function handle(Context $context, $callback):Context;
	
}