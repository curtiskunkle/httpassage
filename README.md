# Quick Router #

Routing library for PHP web applications.  This extends [AltoRouter](https://altorouter.com/) and loosely models some additional functionality after [Express](https://expressjs.com/)

## Usage ##
To use, create an instance of Router, map some routes to it, and call the route() method passing in instances of Request and Response.
```php
//create router
$router = new \QuickRouter\Router();

//map a route
$router->map("GET", "/my/path", function($request, $response) {
	//do some stuff to request or response here
});

//route a request
$req = new \QuickRouter\Request();
$res = new \QuickRouter\Response();
$router->route($req, $res);

//output response to client
$res->respond();
```

## Setting a base path ##
Depending on how your server is configured, you may need to provide a base path.  This will get prepended to route regexs before the router tries to find a match.  For more information on server config and base paths, see the AltoRouter docs.
```php
$router = new \QuickRouter\Router();
$router->setBasePath("/some/path/");
```

## Mapping Routes ##
Routes are defined by passing an HTTP method, route regex, and callback to the map method of Router. Note: AltoRouter compiles route regex and adds some functionality for matching named paramters.  Any named parameters parsed from a matched route will be set on the `$request->routeParams` array

* HTTP Method Examples
	* `GET` -  matches GET request only
	* `POST` - matches POST request only
	* `GET|POST` - matches GET or POST request
	* `GET|POST|PATCH|DELETE` - matches any request with method GET, POST, PATCH, or DELETE
* Route Regex Examples
	* `/path.*` -  matches /path with anything afterwards
	* `/path/?` - matches /path or /path/
	* `/path/[i:id]/?` - matches /path/1 - see AltoRouter docs for more on named params
* Callback Examples
	* Any callable that takes ($request, $response) as parameters.  This includes anonymous functions, class methods, and closures.
		* `function ($request, $response) {...}`- anonymous function
		* `Class::method` - string representing a method on Class
	* An Array of callables as described above, in this case, the callbacks will each be called in the order provided
	* An instance of \QuickRouter\Router (aka subrouter) - in this case, the current Router's basePath will be propagated to the call back router and the request/response will be routed through the callback Router.
	
## Middleware ##
Middleware is a way of applying some shared logic across different routes.  It is simply a callable that takes ($request, $response) as parameters.  Midleware can be applied as described above when mapping routes, or it can be applied to an entire router by passing it to the `useMiddleware` function.  Middleware callbacks registered this way will be executed in the order they are registered. IF a middleware function registered using `useMiddleware` returns false, it will return without attempting to match a route.  This allows for "short circuiting" requests in cases where you may not want to run logic based on the result of some middleware.
```php
class ExampleMiddleware {
	public static function perform($request, $response) {
		//do some stuff to req or res
	}
}

//register middleware to router
$router->useMiddleware("ExampleMiddleware::perform");

//use middleware as callback when mapping route
$router->map("GET", "/some/path/?", "ExampleMiddleware::perform");

//use middleware in array of callbacks when mapping route
$router->map("GET", "/some/path/?", [
	"ExampleMiddleware::perform",
	function($request, $response) {
		//do some other stuff after the Example middleware function runs
	}
]);
```

## Testing ##
Run tests by running `composer test` from the project root directory.  The test file is at tests/QuickRouterTest.php.