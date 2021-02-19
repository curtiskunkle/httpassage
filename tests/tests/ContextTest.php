<?php 

class ContextTest extends \PHPUnit\Framework\TestCase {

    public function testConstructor() {
        $req = Provider::getRequest("POST", "https://example-post");
        $res = Provider::getResponse();
        $res->getBody()->write("test");
        $state = ["state" => "test"];
        $routeParameters = ["id" => 123];
        $matchedRoutes = ["match" => "POST-/"];

        $context = new \HTTPassage\Context(
            $req,
            $res,
            $state,
            $routeParameters,
            $matchedRoutes
        );

        $this->assertEquals($context instanceof \HTTPassage\Context, true);
        $this->assertEquals($context->getRequest()->getMethod(), "POST");
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "test");
        $this->assertEquals($context->getState(), $state);
        $this->assertEquals($context->getRouteParameters(), $routeParameters);
        $this->assertEquals($context->getMatchedRoutes(), $matchedRoutes);
    }

    public function testWithRequest() {
        $context = Provider::getContext();
        $request = $context->getRequest();
        $this->assertEmpty($request->getAttribute("test"));
        $result = $context->withRequest($request->withAttribute("test", "test"));
        $this->assertEquals($result instanceof \HTTPassage\Context, true);
        $this->assertEquals($result->getRequest()->getAttribute("test"), "test");
    }

    public function testWithResponse() {
        $context = Provider::getContext();
        $response = $context->getResponse();
        $this->assertEmpty($response->getBody()->__toString());
        $response->getBody()->write("test body");
        $result = $context->withResponse($response);
        $this->assertEquals($result instanceof \HTTPassage\Context, true);
        $this->assertEquals($result->getResponse()->getBody()->__toString(), "test body");
    }

    public function testWithState() {
        $context = Provider::getContext();
        $state = $context->getState();
        $this->assertEmpty($state);
        $result = $context->withState("test state");
        $this->assertEquals($result instanceof \HTTPassage\Context, true);
        $this->assertEquals($result->getState(), "test state");
    }

    public function testWithRouteParameters() {
        $context = Provider::getContext();
        $routeParams = ["id" => 123, "action" => "test"];
        $this->assertEmpty($context->getRouteParameters());
        $result = $context->withRouteParameters($routeParams);
        $this->assertEquals($result instanceof \HTTPassage\Context, true);
        $this->assertEquals($result->getRouteParameters(), $routeParams);
    }

    public function testGetRouteParameters() {
        $routeParams = ["id" => 123, "action" => "test"];
        $context = Provider::getContext()->withRouteParameters($routeParams);
        $this->assertEquals($context->getRouteParameters(), $routeParams);
        $this->assertEquals($context->getRouteParameter("id"), 123);
        $this->assertEquals($context->getRouteParameter("action"), "test");
        $this->assertEquals($context->getRouteParameter("non_existant_key"), null);
        $this->assertEquals($context->getRouteParameter("non_existant_key", "default"), "default");
    }

    public function testWithMatchedRoutes() {
        $context = Provider::getContext();
        $matchedRoutes = ["GET-/path", "GET-/path/path"];
        $this->assertEmpty($context->getMatchedRoutes());
        $result = $context->withMatchedRoutes($matchedRoutes);
        $this->assertEquals($result instanceof \HTTPassage\Context, true);
        $this->assertEquals($result->getMatchedRoutes(), $matchedRoutes);
    }

    public function testWithExitFlag() {
        $context = Provider::getContext();
        $this->assertEquals($context->shouldExitRouting(), false);
        $context = $context->withExitFlag(true);
        $this->assertEquals($context instanceof \HTTPassage\Context, true);
        $this->assertEquals($context->shouldExitRouting(), true);
        $context = $context->withExitFlag(false);
        $this->assertEquals($context instanceof \HTTPassage\Context, true);
        $this->assertEquals($context->shouldExitRouting(), false);
    }
}