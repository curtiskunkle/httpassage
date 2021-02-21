<h1>Context</h1>

The `\HTTPassage\Context` class represents a request to a server and a corresponding response.  Specifically, these are represented by a PSR-7 ServerRequest and a PSR-7 Response.  The Context class is designed to be immutable and works similarly to PSR7 messages - instead of mutating its state, methods that change a context retain the orginal instance's state and return a new instance with the new state. 

<h3>Class Properties</h3>

|Property|Description|
|---|---|
|request|A PSR-7 ServerRequest|
|response|A PSR-7 Response|
|routeParameters|Named parameters extracted from a matched route path|
|matchedRoutes|Array of matched routes in the format METHOD-path|
|exitRouting|Flag that indicates context should exit router `route` method|
|state|General purpose field for storing data on context|

<h3>Class methods</h3>

|Method|Parameters|Description|
|---|---|---|
|getRequest||return `$this->request`
|getResponse||return `$this->response`
|getState||return `$this->state`
|getRouteParameters||return `$this->routeParameters`
|getRouteParameter|key, default|return the route parameter by key or the default if not exists
|getMatchedRoutes||return `$this->matchedRoutes`
|shouldExitRouting||return `$this->exitRouting`
|withRequest|request|update the context's request
|withResponse|response|update the context's response
|withState|state|update the context's state
|withRouteParameters|routeParameters|update the context's route parameters
|withMatchedRoutes|matchedRoutes|update the context's matched routes
|withExitFlag|flag|update the context's exit flag

<h3>Create Context</h3>

```php
<?php

//create a ServerRequest and Response using any PSR7 implementation
$request = new \GuzzleHttp\Psr7\ServerRequest("GET", "http://examplehost/some/path");
$response = new \GuzzleHttp\Psr7\Response();

//pass them to the constructor
$context = new \HTTPassage\Context($request, $response);
```

<h3>Update Context</h3>

```php
<?php

//update state
$context = $context->withState("Some arbitrary state");

//update response
$response = $context->getResponse();
$context = $context->withResponse($response->withHeader("X-My-Header", "value"));

```