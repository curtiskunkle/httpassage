<?php

require_once(__DIR__ . "/../vendor/autoload.php");

spl_autoload_register(function ($module) {
    if (file_exists(__DIR__ . "/$module.php")) {
        require_once __DIR__ . "/$module.php";
    }
});