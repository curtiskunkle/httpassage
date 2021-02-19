<?php 
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {
    
	public function testMapsRoute() {
		$router = new \HTTPassage\Router();
		$router->map("GET", "/path/", "callback");
        $this->assertEquals($router->getRoutes()[0], [
            "GET",
            "/path/",
            "callback",
            "GET-/path/",
        ]);
	}

    public function testShorthandMapsRoute() {
        $router = new \HTTPassage\Router();
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
        $router = new \HTTPassage\Router();
        $router->addMiddleware(function($context) {});
        $this->assertEquals(is_callable($router->getMiddleware()[0]), true);
    }

    
    public function testAppliesRegisteredMiddleware() {
        $router = new \HTTPassage\Router();
        $router->useMiddleware([
            function ($context) {
                return $context->withRequest($context->getRequest()->withAttribute("test", "test"));
            },
            function ($context) {
                $response = $context->getResponse();
                $response->getBody()->write("test test test");
            },
        ]);
        
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getRequest()->getAttribute("test"), "test");
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "test test test");
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
        $router = new \HTTPassage\Router();
        $router->map("GET", "/path/?", new RequestHandlerInterfaceExample());
        $context = Provider::getContext();
        $context = $router->route($context);
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "Applied request handler");
    }

    public function testAppliesPSR15Middleware1() {
        $router = new \HTTPassage\Router();
        $router->map("GET", "/path/?", new MiddlewareInterfaceExample1());
        $context = Provider::getContext();
        $context = $router->route($context);
        $this->assertEquals($context->getRequest()->getAttribute("test"), "test");
    }

    public function testAppliesPSR15Middleware2() {
        $router = new \HTTPassage\Router();
        $router->map("GET", "/path/?", new MiddlewareInterfaceExample2());
        $context = Provider::getContext();
        $context = $router->route($context);
        $this->assertEquals($context->getResponse()->getHeader("x-some-header")[0], "value");
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "middleware applied");
    }
    
    public function testRouterCallbackHandlerRoutesRequest() {
        $subRouter = new \HTTPassage\Router();
        $subRouter->map("GET", "/path/example/?", [
            new MiddlewareInterfaceExample1(),
            new MiddlewareInterfaceExample2(),
        ]);

        $router = new \HTTPassage\Router();
        $router->map("GET", "/path.*", $subRouter);
        $context = $router->route(Provider::getContext("GET", "http://example.io/path/example"));
        $this->assertEquals($context->getRequest()->getAttribute("test"), "test");
        $this->assertEquals($context->getResponse()->getHeader("x-some-header")[0], "value");
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "middleware applied");
    }
    
    public function testExitFlagAgainstMiddleware() {
        $router = new \HTTPassage\Router();
        $router->addMiddleware(function($context) {
            return $context->withState("test");
        });
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), "test");

        $router = new \HTTPassage\Router();
        $router->addMiddleware(function($context) {
            return $context->withExitFlag();
        });
        $router->addMiddleware(function($context) {
            return $context->withState("test");
        });

        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), null);
    }

    public function testExitFlagAgainstCallbacks() {
        $router = new \HTTPassage\Router();
        $router->map("GET", "/path/?", [
            function ($context) {
                return $context->withState("test");
            }
        ]);
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getState(), "test");

        $router = new \HTTPassage\Router();
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

    public function testUsesCallbackHandlers() {
        $router = new \HTTPassage\Router();
        $router->useCallbackHandlers([
            new \HTTPassage\CallbackHandler\PSR15RequestHandlerCallbackHandler(),
            new \HTTPassage\CallbackHandler\PSR15MiddlewareCallbackHandler(),
        ]);

        $handlers = $router->getCallbackHandlers();
        $this->assertEquals(count($handlers), 2);
        $this->assertEquals($handlers[0] instanceof \HTTPassage\CallbackHandler\PSR15RequestHandlerCallbackHandler, true);
        $this->assertEquals($handlers[1] instanceof \HTTPassage\CallbackHandler\PSR15MiddlewareCallbackHandler, true);
    }

    public function testAddsCallbackHandler() {
        $router = new \HTTPassage\Router();
        $router->addCallbackHandler(new StringCallbackHandler());
        $handlers = $router->getCallbackHandlers();
        $this->assertEquals(count($handlers), 5);
        $this->assertEquals($handlers[4] instanceof StringCallbackHandler, true);
    }

    public function testAppliesCustomCallbackHandler() {
        $router = new \HTTPassage\Router();
        $router->addCallbackHandler(new StringCallbackHandler());
        $router->map("GET", "/path/?", "Test string");
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "Test string");
    }

    public function testAppliesCustomCallbackHandlerInArray() {
        $router = new \HTTPassage\Router();
        $router->addCallbackHandler(new StringCallbackHandler());
        $router->map("GET", "/path/?", [
            function($context) {
                return $context->withState("test");
            },
            "Test string",
        ]);
        $context = $router->route(Provider::getContext());
        $this->assertEquals($context->getResponse()->getBody()->__toString(), "Test string");
        $this->assertEquals($context->getState(), "test");
    }
}