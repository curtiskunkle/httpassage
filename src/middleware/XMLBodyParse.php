<?php 
namespace QuickRouter;
/**
 * Default XML body parse middleware
 */
class XMLBodyParser  {
	public function parse($request, $response) {
		if ($request->getContentType() === 'application/xml') {
			//@todo
		}
	}
}