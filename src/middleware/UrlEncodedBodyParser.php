<?php 
namespace QuickRouter;
/**
 * Default urlencoded body parse middleware
 */
class UrlEncodedBodyParser  {
	public function parse($request, $response) {
		if ($request->getContentType() === 'application/x-www-form-urlencoded') {
			//@todo
		}
	}
}