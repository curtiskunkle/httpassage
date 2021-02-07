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

}