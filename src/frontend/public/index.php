<?php

(new \Phalcon\Debug())->listen(1, 1);

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

//Profiler Imports
use Fabfuel\Prophiler\Profiler;
use Fabfuel\Prophiler\Toolbar;

// Define some absolute path constants to aid in locating resources
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/main');

require_once ROOT_PATH . "/vendor/autoload.php";

// Profiler
$profiler = new Profiler();

//donfig
$config = new Config([
    "ip"=>"192.168.2.6:8080"
]);


// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        ]
    );
    
    $loader->register();
    
    $container = new FactoryDefault();
    $application = new Application($container);
    
    $toolbar = new Toolbar($profiler);
    $toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\Request());

    $container->set(
        'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

//$container->setShared('profiler', $profiler);
$container->setShared('toolbar', $toolbar);
$container->set('config', $config);

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
$container->set(
    "mongo",
    function () {
        $client = new MongoDB\Client("mongodb://root:secret@mongo");
        return $client->frontend;
    }
);
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

// $response = $application->handle(
//     str_replace("/frontend/", "/", $_SERVER["REQUEST_URI"])
// );

try {
    // Handle the request
    $response = $application->handle(
        str_replace("/frontend/", "/", $_SERVER["REQUEST_URI"])
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
