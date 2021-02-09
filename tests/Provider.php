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
        return \QuickRouter\Factory::createContext(
            Provider::getRequest($method, $url),
            Provider::getResponse()
        );  
    }

    public static function getMatchTestRoutes() {
        return [ 
            ["GET", "/path/test", 1],
            ["POST", "/path/test", 2],
            ["PATCH", "/path/test", 3],
            ["GET|POST|PATCH|DELETE|OPTIONS", "/path/test", 4],
            ["GET", "/path/test/[i:id]", 5],
            ["GET", "/path/test/[i:id]/test", 6],
            ["GET", "/path/test/[i:id]/test/[a:action]", 7],
        ];
    }

    public static function getTestRouter() {
        $router = \QuickRouter\Factory::createRouter();

        foreach (Provider::getTestRoutes() as $route) {
            $router->map($route[0], $route[1], function($context) {
                return $context->withState($route[2]);
            });
        }
    }
}