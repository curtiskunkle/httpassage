<?php

class Provider {

    const DEFAULT_METHOD = "GET";
    const DEFAULT_URL = "http://example.io/path";

    public static function getRequest($method = Provider::DEFAULT_METHOD, $url = Provider::DEFAULT_URL) {
        return new \GuzzleHttp\Psr7\ServerRequest($method, $url);
    }

    public static function getResponse() {
        return new \GuzzleHttp\Psr7\Response();
    }

    public static function getContext($method = Provider::DEFAULT_METHOD, $url = Provider::DEFAULT_URL) {
        return new \QuickRouter\Context(
            Provider::getRequest($method, $url),
            Provider::getResponse()
        );  
    }

    public static function getTestRouter() {
        $router = new \QuickRouter\Router();

        $routes = [ 
            ["GET", "/path/test", 1],
            ["POST", "/path/test", 2],
            ["PATCH", "/path/test", 3],
            ["GET|POST|PATCH|DELETE|OPTIONS", "/path/test", 4],
            ["GET", "/path/test/[i:id]", 5],
            ["GET", "/path/test/[i:id]/test", 6],
            ["GET", "/path/test/[i:id]/test/[a:action]", 7],
            ["GET", "/optional/trailing/slash/?", 15],
        ];

        foreach ($routes as $route) {
            $router->map($route[0], $route[1], function($context) use ($route) {
                return $context->withState($route[2]);
            });
        }

        // $router = new \QuickRouter\Router();

        // $routes = [ 
        //     ["GET", "/path/test", 8],
        //     ["POST", "/path/test", 9],
        //     ["PATCH", "/path/test", 10],
        //     ["GET|POST|PATCH|DELETE|OPTIONS", "/path/test", 11],
        //     ["GET", "/path/test/[i:id2]", 12],
        //     ["GET", "/path/test/[i:id2]/test", 13],
        //     ["GET", "/path/test/[i:id2]/test/[a:action2]", 14],
        // ];

        // foreach ($routes as $route) {
        //     $subRouter->map($route[0], $route[1], function($context) use ($route) {
        //         return $context->withState($route[2]);
        //     });
        // }

        // $router->map("GET|POST|PATCH|DELETE|OPTIONS", "/subrouter.*", new \QuickRouter\Middleware\SubRouter($subRouter));

        return $router;
    }
}