# HTTPassage #

![Tag](https://img.shields.io/github/v/tag/curtiskunkle/httpassage)
![License](https://img.shields.io/github/license/curtiskunkle/httpassage)

A simple, flexible library for routing and processing PSR-7 HTTP messages.

### Features ###
- HTTP request routing built on top of [AltoRouter](https://github.com/dannyvankooten/AltoRouter)
- Compatible with any [PSR-7](https://www.php-fig.org/psr/psr-7/) implementation
- Customizable callback system for processing requests and responses
- Compatible with [PSR-15](https://www.php-fig.org/psr/psr-15/) middleware and request handlers out of the box

### Installation
```shell
composer require ckunkle/httpassage
```

### Quickstart ###
This example uses `guzzlehttp/psr7` for the PSR-7 implementation, but any PSR-7 implementation will work here.

```php
<?php

require __DIR__ . "/vendor/autoload.php";

/**
 * Create an instance of Context by passing a PSR-7 ServerRequest 
 * and a PSR-7 response to the constructor
 */
new \HTTPassage\Context(
    \GuzzleHttp\Psr7\ServerRequest::fromGlobals(),
    new \GuzzleHttp\Psr7\Response()
);  

/**
 * Create a Router instance and map some routes
 */
$router = new \HTTPassage\Router();

$router->get("/my/example/[a:route]", function($context) {
    $route = $context->getRouteParamater("route");
    $context->getResponse()->getBody()->write("You have reached $route!");
    return $context;
});

/**
 * Route the Context
 */
 $context = $router->route($context);
```

### Documentation ###
- [Rewriting Requests](docs/rewritingrequests/README.md)
- [Context](docs/context/README.md)
- [Mapping Routes](docs/routemapping/README.md)
- [Route Processing](docs/routing/README.md)
- [Callback Handlers](docs/callbackhandlers/README.md)
- [Middleware](docs/middleware/README.md)
