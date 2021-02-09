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

    public function testMatchesRoute() {
            // ["GET", "/path/test", 1],
            // ["POST", "/path/test", 2],
            // ["PATCH", "/path/test", 3],
            // ["GET|POST|PATCH|DELETE|OPTIONS", "/path/test", 4],
            // ["GET", "/path/test/[i:id]", 5],
            // ["GET", "/path/test/[i:id]/test", 6],
            // ["GET", "/path/test/[i:id]/test/[a:action]", 7],

            // subroutes
            // ["GET", "/path/test", 8],
            // ["POST", "/path/test", 9],
            // ["PATCH", "/path/test", 10],
            // ["GET|POST|PATCH|DELETE|OPTIONS", "/path/test", 11],
            // ["GET", "/path/test/[i:id2]", 12],
            // ["GET", "/path/test/[i:id2]/test", 13],
            // ["GET", "/path/test/[i:id2]/test/[a:action2]", 14],

        $router = Provider::getTestRouter();
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test"))->getState(), 1);
        $this->assertEquals($router->route(Provider::getContext("POST", "/path/test"))->getState(), 2);
        $this->assertEquals($router->route(Provider::getContext("PATCH", "/path/test"))->getState(), 3);
        $this->assertEquals($router->route(Provider::getContext("OPTIONS", "/path/test"))->getState(), 4);
        $this->assertEquals($router->route(Provider::getContext("DELETE", "/path/test"))->getState(), 4);
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test/1"))->getState(), 5);
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test/1/test"))->getState(), 6);
        $this->assertEquals($router->route(Provider::getContext("GET", "/path/test/1/test/action"))->getState(), 7);

        //@todo subrouters not working as expected
        $this->assertEquals($router->route(Provider::getContext("GET", "/subrouter/path/test"))->getState(), 8);
    }

    // public function testHandleTrailingSlash() {

    // }

    // public function testAssignsMatchedRoute() {

    // }

    // public function testDoesNotMatchRoute() {

    // }

    // public function testAssignsNamedParamsToRequest() {
 
    // }

    // //@todo test every type of middleware
    // public function testAppliesMiddleware() {

    // }

    // public function testUsesBasePath() {

    // }

    // public function testPassesBasePathToSubrouter() {
        
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