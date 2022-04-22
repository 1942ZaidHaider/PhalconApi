<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Exception as MicroException;
use Phalcon\Config;
use MongoDB\Client;
use Phalcon\Mvc\View\Simple;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

$config = new Config([]);

define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', dirname(dirname(__DIR__)));
require_once ROOT_PATH . "/vendor/autoload.php";
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
        return $client->api;
    }
);

$container->set('view', function () {
    $view = new Simple();
    $view->setViewsDir(BASE_PATH . '/views/');
    return $view;
}, true);

$container->set(
    "session",
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session->setAdapter($files);
        $session->start();
        return $session;
    }
);


// Handling requests

$prodHandler = new ProductHandler();
$orderHandler = new OrderHandler();
$authHandler = new AuthHandler();
$prodHandler->initialize();
$orderHandler->initialize();

$api->before(
    function () use ($api) {
        $url = explode("/", $_SERVER['REQUEST_URI']);
        $key = "raxacoricofallapatorian";
        $exempt = [
            "auth",
            "register"
        ];
        $auth = 0;
        if ($url[1] != 'api') {
            foreach ($exempt as $e) {
                if (str_contains($url[2], $e)) {
                    $auth = 1;
                    break;
                }
            }
        } else {
            $auth = 1;
        }
        return (new EventListener())->beforeExecuteRoute($api, $auth, $key);
    }
);
/**
 * HELP ENDPOINT
 */
$api->get(
    "/api",
    [
        $authHandler,
        "index"
    ]
);
/**
 * Product endpoints
 */
$api->post(
    "/api/insert",
    [
        $prodHandler,
        "insert"
    ]
);
$api->get(
    "/api/products/search/{search}",
    [
        $prodHandler,
        "search"
    ]
);
$api->get(
    "/api/products/search",
    [
        $prodHandler,
        "search"
    ]
);
$api->get(
    "/api/products/get",
    [
        $prodHandler,
        "get"
    ]
);
$api->get(
    "/api/products/get/{id}",
    [
        $prodHandler,
        "get"
    ]
);
/**
 * Authorization endpoints
 */
$api->post(
    "/api/auth",
    [
        $authHandler,
        "auth"
    ]
);

$api->get(
    "/api/register",
    [
        $authHandler,
        "register"
    ]
);
/**
 * Order endpoints
 */
$api->get(
    "/api/orders/get",
    [
        $orderHandler,
        "get"
    ]
);
$api->post(
    "/api/orders/create",
    [
        $orderHandler,
        "create"
    ]
);

$api->put(
    "/api/orders/update",
    [
        $orderHandler,
        "update"
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
