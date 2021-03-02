<h1>Middleware</h1>

In the context of this library, middleware refers to any callback that should be applied to a `Context` before it is routed.  This can be useful in scenarios in which you would like to apply a callback to every request. Some examples are body parsing, logging, dependency injection, etc. Middleware allows you to do this without assigning the callback to every single mapped route.  The `Router` class exposes two methods for configuring middleware and one for getting the registered middleware callbacks:

1. `addMiddleware` -  adds a middleware to the router's middleware array
2. `useMiddleware` - sets the router's middleware array to the provided midleware array
3. `getMiddleware` - returns the middleware array

The router uses the same callback handlers to process middleware as it does to process callbacks assigned to mapped routes.  So, by default, you can assign any callback that can be handled by the default callback handlers or an array of such callbacks.  If you have assigned additional callback handlers to the router, they will also be available for processing middleware.

<h3>addMiddleware Example</h3>

```php 
<?php

$router = new \HTTPassage\Router();

/**
 * Use the addMiddleware method to add a callback that should apply to every context
 */
$router->addMiddleware(function($context) {
    return $context->withState([
        "This callback will be applied as soon as the context is",
        "passed into the route method of the router whether there",
        "is a matched route or not.",
    ]);
});

```

<h3>useMiddleware Example</h3>

```php 
<?php

$router = new \HTTPassage\Router();

/**
 * Use the useMiddleware method to set the middleware array.
 * The callbacks will be applied in order before the context 
 * is routed through the mapped routes.
 */
$router->addMiddleware([
    function($context) {
        $context->getResponse()->getBody()->write("This callback was applied first! ");
        return $context;
    },
    function($context) {
        $context->getResponse()->getBody()->write("This callback was applied second!");
        return $context;
    },
]);

```
