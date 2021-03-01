<h1>Callback Handlers</h1>

<h3>Overview</h3>

`HTTPassage` uses a system of callback handlers to determine how to process a `Context` instance once a matched route has been found during routing.  A callback handler is an instance of a class that implements `HTTPassage\CallbackHandler\CallbackHandlerInterface`.  This interface requires two functions: 

1. `meetsCriteria`
2. `handle`

`meetsCriteria` is a function that accepts a single parameter - $callback and returns a boolean.  $callback will be the callback that is passed to a mapping function when mapping routes to a router.  So, in the following example, $myCallback would get passed to the `meetsCriteria` function of each callback handler assigned to the router.

```php
<?php

$router = new \HTTPassage\Router();

$myCallback = function($context) {
    $context->getResponse()->getBody()->write("Callback has been handled.");
};

$router->get("/example/path", $myCallback);
```

If `meetsCriteria` returns true, then the `Context` instance is processed by passing the context and the callback to the `handle` function of the callback handler.  Note: when checking against all callback handlers on the router, the first callback handler that returns true for `meetsCriteria` will be used.

<h3>Default Callback Handlers</h3>

`HTTPassage` comes out of the box with a few default callback handlers.  These are registered to a router as soon as it is constructed.

|Handler|`meetsCriteria`Description|`Handle` Description|
|---|---|---|
|CallableCallbackHandler|Returns `is_callable($callback)`|Passes context to the callable
|PSR15MiddlewareCallbackHandler|Returns `$callback instanceof \Psr\Http\Server\MiddlewareInterface`|Captures changes to the request and response, and applies them to the context.
|PSR15RequestHandlerCallbackHandler|Returns `$callback instanceof \Psr\Http\Server\RequestHandlerInterface`|Sets the context response to the return value of the request handler's handle method
|RouterCallbackHandler|Returns `$callback instanceof \HTTPassage\Router`|Routes the context through a subrouter and returns it.

<h3>Arrays of Callbacks</h3>
It is also possible to map an array of callbacks to a route.  This allows you to abstract small pieces of shared logic and use them to compose routes in the form of middleware.  The array can contain any callback that can be handled by a callback handler registered to the router.  When a matched route has an array of callbacks assigned, the context is processed through each callback sequentially <b>unless the exit flag gets set on the context</b>

```php
<?php

$router = new \HTTPassage\Router();

/**
 * This example route will apply a PSR15Middleware, a PSR15RequestHandler, and 
 * a callable before returning the Context.
 */
$router->get("/example/path", [
    new SomePRS15Middleware(),
    new SomePSR15RequestHandler() 
    function($context) {
        $context->getResponse()->getBody()->write("Callback has been handled.");
    }
]);
```

<h3>Custom Callback Handlers</h3>

todo