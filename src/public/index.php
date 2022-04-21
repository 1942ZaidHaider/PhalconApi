<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Exception as MicroException;
use Phalcon\Config;
use MongoDB\Client;
use Firebase\JWT\JWT;
use Phalcon\Mvc\View\Simple;

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

$container->set('view', function () {
    $view = new Simple();
    $view->setViewsDir(BASE_PATH . '/views/');
    return $view;
}, true);

// Handling requests

$handler = new Handler();
$handler->initialize();
$api->before(
    function () use ($api) {
        $url = explode("/", $_SERVER['REQUEST_URI']);
        $key = "raxacoricofallapatorian";
        $exempt = [
            "auth",
            "register"
        ];
        $auth = 0;
        foreach ($exempt as $e) {
            if ($url[1] == $e) {
                $auth = 1;
                break;
            }
        }
        $auth = ($url[1] == "auth" || count($url));
        return (new EventListener())->beforeExecuteRoute($api, $auth, $key);
    }
);
$api->get(
    "/",
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
    "/products/search",
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

$api->get(
    "/register",
    [
        $handler,
        "register"
    ]
);



try {
    // Handle the request
    $api->handle(
        $_SERVER["REQUEST_URI"]
    );
} catch (MicroException $e) {
    $api->response->setStatusCode(404, "Not Found");
    echo json_encode([
        "status" => $api->response->getStatusCode(),
        "message" => "URL not found"
    ]);
    $api->response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
