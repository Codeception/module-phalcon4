<?php
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql;

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
return new Application($di);
