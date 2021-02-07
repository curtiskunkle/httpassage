<?php 

class FactoryTest extends \PHPUnit\Framework\TestCase {

	public function testCreateContext() {
        $req = Provider::getRequest("POST", "https://example-post");
        $res = Provider::getResponse();
        $res->getBody()->write("test");
        $state = ["state" => "test"];
        $routeParameters = ["id" => 123];
        $matchedRoutes = ["match" => "POST-/"];

        $context = \QuickRouter\Factory::createContext(
        	$req,
        	$res,
        	$state,
        	$routeParameters,
        	$matchedRoutes,
        	true
        );

        $this->assertEquals($context instanceof \QuickRouter\Context, true);
        $this->assertEquals($context->getRequest()->getMethod(), "POST");
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "test");
        $this->assertEquals($context->getState(), $state);
        $this->assertEquals($context->getRouteParameters(), $routeParameters);
        $this->assertEquals($context->getMatchedRoutes(), $matchedRoutes);
        $this->assertEquals($context->shouldExitRouting(), true);
    }

    public function testCreateContextWithOnlyRequestAndResponse() {
    	$req = Provider::getRequest("POST", "https://example-post");
        $res = Provider::getResponse();

        $context = \QuickRouter\Factory::createContext($req, $res);

        $this->assertEquals($context instanceof \QuickRouter\Context, true);
        $this->assertEquals($context->getRequest()->getMethod(), "POST");
        $this->assertEmpty($context->getResponse()->getBody()->__toString());
        $this->assertEmpty($context->getState());
        $this->assertEmpty($context->getRouteParameters());
        $this->assertEmpty($context->getMatchedRoutes());
        $this->assertEquals($context->shouldExitRouting(), false);
    }

    public function testCreateRouter() {
    	$router = \QuickRouter\Factory::createRouter();
    	$this->assertEquals($router instanceof \QuickRouter\Router, true);
    }

}