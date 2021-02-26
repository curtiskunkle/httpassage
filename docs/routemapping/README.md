<h1>Mapping Routes</h1>

The `HTTPassage\Router` class is an extension of [AltoRouter](https://github.com/dannyvankooten/AltoRouter), a light-weight, regex-based routing library with some nice features for route mapping and extracting named parameters from uris.  As such, the AltoRouter documentation is a better reference for how mapping and matching routes works.  Nevertheless, here are a few quick examples.

<h3>Creating Routes</h3>

Creating routes means mapping one or more request methods and a path to a callback.  To create routes, you can use the `map` method, or you can use one of the 6 provided shorthand methods. The `map` method allows for mapping multiple request types by providing a pipe-delimited list.

```php
<?php 
/**
 * Request method examples
 */
$router = new \HTTPassage\Router();

$myCallback = function($context) {
    return $context->withState("This context was routed!");
};

//map a GET request using the map method
$router->map("GET", "/a/path", $myCallback);

//map a GET or POST or PATCH request using the map method
$router->map("GET|POST|PATCH", "/a/path", $myCallback);

//use one of the 6 shorthand methods
$router->get("/a/path", $myCallback);
$router->post("/a/path", $myCallback);
$router->put("/a/path", $myCallback);
$router->delete("/a/path", $myCallback);
$router->patch("/a/path", $myCallback);
$router->options("/a/path", $myCallback);
```

<h3>Route paths</h3>

```php
<?php

$router = new \HTTPassage\Router();

$myCallback = function($context) {
    return $context->withState("This context was routed!");
};

//matches  /my/path exactly
$router->get("/my/path", $myCallback);

//matches /my/path or my/path/
$router->get("/my/path[/]?", $myCallback);

//matches /my/path or my/path with any trailing string
$router->get("/my/path[*]?", $myCallback);

//matches /my/path/1 and adds id to context route parameters
$router->get("/my/path/[i:id]", $myCallback);

//matches /my/path/action and adds action to context route parameters
$router->get("/my/path/[a:action]", $myCallback);

//custom regex start with @
$router->get("@/my/.+?/custom/.*/regex?", $myCallback);
```

<h3>Base Path</h3>

You can optionally set a base path that will be prepended to route paths when matching against requests.  This is useful if the file you are rewriting requests to is not in the root of your project.

```php
<?php

//create router
$router = new \HTTPassage\Router();

//set base path
$router->setBasePath("/project-dir/");

//matches request with path /project-dir/path
$router->get("/path", function($context) {
    //...
    return $context;
});

```