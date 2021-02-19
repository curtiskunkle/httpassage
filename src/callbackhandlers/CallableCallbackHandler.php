<?php

namespace HTTPassage\CallbackHandler;
use HTTPassage\ContextInterface as Context;

/**
 * Handles callable. The callable should be a function that accepts and returns Context
 * @example
 * function($context) {
 * 		return $context->withState("Some State");
 * }
 */
class CallableCallbackHandler implements CallbackHandlerInterface {

	public function meetsCriteria($callback): bool {
		return is_callable($callback);
	}

	public function handle(Context $context, $callback):Context {
		$result = call_user_func_array($callback, [$context]);
		return $result instanceof Context ? $result : $context;
	}
	
}