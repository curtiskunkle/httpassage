<?php

namespace HTTPassage\CallbackHandler;
use HTTPassage\ContextInterface as Context;

class RouterCallbackHandler implements CallbackHandlerInterface {

	public function meetsCriteria($callback): bool {
		return $callback instanceof \HTTPassage\Router;
	}

	public function handle(Context $context, $callback):Context {
		return $callback->route($context);
	}
	
}