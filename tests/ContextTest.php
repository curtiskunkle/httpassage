<?php 
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../vendor/autoload.php");

class ContextTest extends TestCase {

    function getContext() {
        $req = new \GuzzleHttp\Psr7\ServerRequest("GET", "http://a-host/quick-router/a/path");
        $res = new \GuzzleHttp\Psr7\Response();
        return \QuickRouter\Factory::createContext($req, $res);  
    }

    function testWithRequest() {
        $context = $this->getContext();
        $request = $context->getRequest();
        $result = $context->withRequest($request->withAttribute("test", "test"));
        $this->assertEquals($result instanceof \QuickRouter\Context, true);
        $this->assertEquals($result->getRequest()->getAttribute("test"), "test");
    }

    function testWithResponse() {
        $context = $this->getContext();
        $response = $context->getResponse();
        $response->getBody()->write("test body");
        $result = $context->withResponse($response);
        $this->assertEquals($result instanceof \QuickRouter\Context, true);
        $this->assertEquals($result->getResponse()->getBody()->__toString(), "test body");
    }
}