<?php

use HTTPassage\ContextInterface as Context;
use HTTPassage\CallbackHandler\CallbackHandlerInterface as CallbackHandler;

class StringCallbackHandler implements CallbackHandler {

	public function meetsCriteria($callback): bool {
		return is_string($callback);
	}

	public function handle(Context $context, $callback):Context {
		$context->getResponse()->getBody()->write($callback);
		return $context;
	}
	
}