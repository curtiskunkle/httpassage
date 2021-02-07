<?php 
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

    /****************
	 * ROUTER TESTS 
	 ****************/

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

    // public function testMatchesRoute() {
    //     $this->setMethodAndPath("GET", "/path/");
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/path/", function($request, $response) {
    //         $response->data = "test";
    //     });
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($result, true);
    //     $this->assertEquals($response->data, "test");
    //     $this->assertEquals($response->getCode(), 200);
    // }

    // public function testDoesNotMatchRoute() {
    //     $this->setMethodAndPath("GET", "/path/");
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/not-path/", function($request, $response) {
    //         $response->data = "test";
    //     });
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($result, false);
    //     $this->assertEquals($response->data, []);
    //     $this->assertEquals($response->getCode(), 404);
    // }

    // public function testAssignsNamedParamsToRequest() {
    //     $this->setMethodAndPath("GET", "/path/1");
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/path/[i:id]", function($request, $response) {});
    //     $router->route($request, $response);
    //     $this->assertEquals($request->routeParams, ["id" => 1]);
    // }

    // public function testAppliesMiddleware() {
    //     $this->setMethodAndPath("GET", "/path/");

    //     //anon function middleware
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->useMiddleware(function($request, $response) {
    //         $request->test = 1;
    //         $response->test = 1;
    //     });
    //     $router->map("GET", "/path/", function($request, $response) {
    //         $response->data = "test";
    //     });
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($response->test, 1);
    //     $this->assertEquals($request->test, 1);
    //     $this->assertEquals($response->data, "test");

    //     //middleware defined as class method
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->useMiddleware("TestMiddleware::perform");
    //     $router->map("GET", "/path/", function($request, $response) {
    //         $response->data = "test";
    //     });
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($response->test, 1);
    //     $this->assertEquals($request->test, 1);
    //     $this->assertEquals($response->data, "test");
    // }

    // public function testAppliesCallbacks() {
    //     $this->setMethodAndPath("GET", "/path/");

    //     //test single callback anonymous function
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/path/", function($request, $response) {
    //         $response->test = 1;
    //         $request->test = 1;
    //     });
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($result, true);
    //     $this->assertEquals($response->test, 1);
    //     $this->assertEquals($request->test, 1);

    //     //test single callback class method
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/path/", "TestMiddleware::perform");
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($result, true);
    //     $this->assertEquals($response->test, 1);
    //     $this->assertEquals($request->test, 1);
        
    //     //test array of callbacks
    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/path/", [
    //         "TestMiddleware::perform",
    //         function($request, $response) {
    //             $response->test2 = 1;
    //             $request->test2 = 1;
    //         }
    //     ]);
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($result, true);
    //     $this->assertEquals($response->test, 1);
    //     $this->assertEquals($request->test, 1);
    //     $this->assertEquals($response->test2, 1);
    //     $this->assertEquals($request->test2, 1);

    //     $this->setMethodAndPath("GET", "/path/1");

    //     //test subrouter
    //     $subRouter = new \QuickRouter\Router();
    //     $subRouter->map("GET", "/path/[i:id]", function($request, $response) {
    //         $response->subRouterApplied = 1;
    //     });

    //     list($router, $request, $response) = $this->getRouterObjects();
    //     $router->map("GET", "/path.*", $subRouter);
    //     $result = $router->route($request, $response);
    //     $this->assertEquals($result, true);
    //     $this->assertEquals($response->subRouterApplied, 1);
    //     $this->assertEquals($request->routeParams, ["id" => 1]);
    // }
}