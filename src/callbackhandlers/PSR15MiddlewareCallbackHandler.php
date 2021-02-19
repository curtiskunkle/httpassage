<?php

namespace HTTPassage\CallbackHandler;
use HTTPassage\ContextInterface as Context;

/**
 * Handles PSR-15 Middleware callback
 * Captures changes to request and response, and sets the new request/response on Context
 */
class PSR15MiddlewareCallbackHandler implements CallbackHandlerInterface {

	public function meetsCriteria($callback): bool {
		return $callback instanceof \Psr\Http\Server\MiddlewareInterface;
	}

	public function handle(Context $context, $callback):Context {
		$handler = new \HTTPassage\Middleware\PassThroughRequestHandler();
		$handler->setRequest($context->getRequest());
		$handler->setResponse($context->getResponse());
		$response = $callback->process($context->getRequest(), $handler);
		if ($response instanceof \Psr\Http\Message\ResponseInterface) {
			$context = $context->withResponse($response);
		}
		$handledRequest = $handler->getRequest();
		if ($handledRequest instanceof \Psr\Http\Message\ServerRequestInterface) {
			$context = $context->withRequest($handledRequest);
		}
		return $context;
	}
}