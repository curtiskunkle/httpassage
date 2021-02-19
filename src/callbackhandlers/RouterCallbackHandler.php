<?php

namespace QuickRouter\CallbackHandler;
use QuickRouter\ContextInterface as Context;

class RouterCallbackHandler implements CallbackHandlerInterface {

	public function meetsCriteria($callback): bool {
		return $callback instanceof \QuickRouter\Router;
	}

	public function handle(Context $context, $callback):Context {
		return $callback->route($context);
	}
	
}