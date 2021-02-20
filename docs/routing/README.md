<h1>HTTPassage <span style="color:gray; font-size: .7em"> Routing Requests</span></h1>

After your router has some routes mapped to it, it can be used to route a context.  The `route` method of `\HTTPassage\Router` accepts and returns a context.  The logic for the `route` method is as follows:

1. The method and path of the context's request are compared against each mapped route to determine if there is a match.  The routes are compared in the order that they were mapped, and the first match will be used if one is found.
2. Any middleware registered to the router using `useMiddleware` or `addMiddleware` are applied to the context. 
3. If not match is found, a 404 is set on the context response, and the context is returned.
4. If a match is found, the corresponding callback will be applied to the context before it is returned.

If any callback sets the exit flag to true on the context, the context will return before any other callbacks are applied.


```php
<?php

$router = new \HTTPassage\Router();

//map some routes here
//$router->map(...);

//create a ServerRequest and Response using any PSR7 implementation
$request = new \GuzzleHttp\Psr7\ServerRequest("GET", "http://examplehost/some/path");
$response = new \GuzzleHttp\Psr7\Response();

//create a request context
$context = new \HTTPassage\Context($request, $response);

$context = $router->route($context);
```