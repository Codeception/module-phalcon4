<?php

use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Url as UrlProvider;

$di = new FactoryDefault();
$di->setShared(
    'session',
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

$di->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => getenv('DB_HOST'),
                'port'     => getenv('DB_PORT'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'dbname'   => getenv('DB_NAME'),
            ]
        );
    }
);

/**
 * Setting the View
 */
$di->setShared('view', function () {
    $view = new View();
    return $view;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $url = new UrlProvider();
    $url->setBaseUri('/');
    return $url;
});


$router = $di->getRouter();

$router->add('/contact', [
    'controller' => 'App\Controllers\Contact',
    'action'     => 'index'
])->setName('front.contact');


return new Application($di);
