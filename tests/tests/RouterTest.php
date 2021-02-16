<?php 
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {
    
	public function testMapsRoute() {
		$router = new \QuickRouter\Router();
		$router->map("GET", "/path/", "callback");
        $this->assertEquals($router->getRoutes()[0], [
            "GET",
            "/path/",
            "callback",
            "GET-/path/",
        ]);
	}

    public function testShorthandMapsRoute() {
        $router = new \QuickRouter\Router();
        $router->get("/path/", "callback");
        $router->post("/path/", "callback");
        $router->put("/path/", "callback");
        $router->patch("/path/", "callback");
        $router->options("/path/", "callback");
        $router->delete("/path/", "callback");
        foreach (["get", "post", "put", "patch", "options", "delete"] as $key => $method) {
            $this->assertEquals($router->getRoutes()[$key], [
                strtoupper($method),
                "/path/",
                "callback",
                strtoupper($method) . "-/path/",
            ]);
        }
    }

    public function testRegistersMiddleware() {
        $router = new \QuickRouter\Router();
        $router->useMiddleware(function($context) {});
        $this->assertEquals(is_callable($router->middleware[0]), true);
    }

    //@todo
    public function testAppliesRegisteredMiddleware() {

    }

    public function testMatchesRoute() {
        $router = Provider::getTestRouter();
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test"))->getState(), 1);
        $this->assertEquals($router->route(Provider::getContext("POST", "/path/test"))->getState(), 2);
        $this->assertEquals($router->route(Provider::getContext("PATCH", "/path/test"))->getState(), 3);
        $this->assertEquals($router->route(Provider::getContext("OPTIONS", "/path/test"))->getState(), 4);
        $this->assertEquals($router->route(Provider::getContext("DELETE", "/path/test"))->getState(), 4);
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test/1"))->getState(), 5);
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test/1/test"))->getState(), 6);
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test/1/test/action"))->getState(), 7);
    }

    public function testHandleTrailingSlash() {
        $router = Provider::getTestRouter();
        $this->assertEquals($router->route(Provider::getContext("GET", "/optional/trailing/slash/"))->getState(), 15);
        $this->assertEquals($router->route(Provider::getContext("GET", "/optional/trailing/slash"))->getState(), 15);
    }

    public function testAssignsMatchedRoute() {
        $context = Provider::getTestRouter()->route(Provider::getContext("GET", "/path/test"));
        $this->assertEquals($context->getMatchedRoutes(), ["GET-/path/test"]);
    }

    public function testDoesNotMatchRoute() {
        $context = Provider::getTestRouter()->route(Provider::getContext("GET", "/invalid/route"));
        $this->assertEquals($context->getResponse()->getStatusCode(), 404);
        $this->assertEquals($context->getState(), null);
        $this->assertEquals($context->getMatchedRoutes(), []);
    }

    public function testAssignsNamedParamsToRequest() {
        $router = Provider::getTestRouter();
        $context = $router->route(Provider::getContext("GET", "/path/test/1"));
        $this->assertEquals($context->getRouteParameters(), ["id" => "1"]);
        $this->assertEquals($context->getRouteParameter("id"), "1");

        $context = $router->route(Provider::getContext("GET", "/path/test/1/test/action"));
        $this->assertEquals($context->getRouteParameters(), ["id" => "1", "action" => "action"]);
        $this->assertEquals($context->getRouteParameter("id"), "1");
        $this->assertEquals($context->getRouteParameter("action"), "action");
    }

    public function testAppliesPSR15RequestHandler() {
        $router = new \QuickRouter\Router();
        $router->map("GET", "/path/?", new RequestHandlerInterfaceExample());
        $context = Provider::getContext();
        $context = $router->route($context);
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "Applied request handler");
    }

    public function testAppliesPSR15Middleware1() {
        $router = new \QuickRouter\Router();
        $router->map("GET", "/path/?", new MiddlewareInterfaceExample1());
        $context = Provider::getContext();
        $context = $router->route($context);
        $this->assertEquals($context->getRequest()->getAttribute("test"), "test");
    }

    //@todo more psr15 middleware tests
    
    public function testExitFlagAgainstMiddleware() {
        $router = new \QuickRouter\Router();
        $router->useMiddleware(function($context) {
            return $context->withState("test");
        });
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), "test");

        $router = new \QuickRouter\Router();
        $router->useMiddleware(function($context) {
            return $context->withExitFlag();
        });
        $router->useMiddleware(function($context) {
            return $context->withState("test");
        });

        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), null);
    }

    public function testExitFlagAgainstCallbacks() {
        $router = new \QuickRouter\Router();
        $router->map("GET", "/path/?", [
            function ($context) {
                return $context->withState("test");
            }
        ]);
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), "test");

        $router = new \QuickRouter\Router();
        $router->map("GET", "/path/?", [
            function ($context) {
                return $context->withExitFlag();
            },
            function ($context) {
                return $context->withState("test");
            }
        ]);
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), null);
    }
}