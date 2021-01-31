<?php 
namespace QuickRouter;
/**
 * Default JSON body parse middleware
 */
class JsonBodyParser  {
	public function parse($request, $response) {
		if ($request->getContentType() === 'application/json') {
			$body = json_decode($request->rawInput, true);
			$request->body = is_array($body) ? $body : null;
		}
	}
}