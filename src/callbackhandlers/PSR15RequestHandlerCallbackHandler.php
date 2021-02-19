<?php

namespace HTTPassage\CallbackHandler;
use HTTPassage\ContextInterface as Context;

/**
 * Handles a PSR-15 RequestHandler.  If the handler returns a response, sets 
 * the new response on the context.
 */
class PSR15RequestHandlerCallbackHandler implements CallbackHandlerInterface {

	public function meetsCriteria($callback): bool {
		return $callback instanceof \Psr\Http\Server\RequestHandlerInterface;
	}

	public function handle(Context $context, $callback):Context {
		$response = $callback->handle($context->getRequest());
		return $response instanceof \Psr\Http\Message\ResponseInterface
			? $context->withResponse($response)
			: $context;
	}
}