<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Config;
use MongoDB\Client;
use Firebase\JWT\JWT;

$config = new Config([]);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . "/vendor/autoload.php";
$loader = new Loader();
$loader->registerDirs(
    [
        BASE_PATH . "/handlers/",
        BASE_PATH . "/listeners/",
    ]
);
$loader->register();

$container = new FactoryDefault();
$api = new Micro($container);

$container->set(
    "mongo",
    function () {
        $client = new Client("mongodb://root:secret@mongo");
        return $client->table;
    }
);

$container->set(
    "jwt",
    function () {
        $key = "raxacoricofallapatorian";
        $currentTime = time();
        $expiry = $currentTime + (24 * 3600);
        $payload = [
            "iss" => '/',
            "aud" => '/',
            "iat" => $currentTime,
            "exp" => $expiry,
            "seed" => rand(99, 999),
        ];
        return JWT::encode($payload, $key, 'HS256');
    }
);
// Handling requests

$handler = new Handler();
$handler->initialize();
$api->before(
    function () use ($api) {
        $url=explode("/",$_SERVER['REQUEST_URI']);
        $key="raxacoricofallapatorian";
        return (new EventListener())->beforeExecuteRoute($api,$url[1]=="auth",$key);
    }
);
$api->get(
    "/index",
    [
        $handler,
        "index"
    ]
);

$api->post(
    "/insert",
    [
        $handler,
        "insert"
    ]
);
$api->get(
    "/products/search/{search}",
    [
        $handler,
        "search"
    ]
);
$api->get(
    "/products/get",
    [
        $handler,
        "get"
    ]
);

$api->post(
    "/auth",
    [
        $handler,
        "auth"
    ]
);

try {
    // Handle the request
    $api->handle(
        $_SERVER["REQUEST_URI"]
    );
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
