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
                'host'     => '127.0.0.1',
                'port'     => getenv('PORT'),
                'username' => 'root',
                'password' => 'password',
                'dbname'   => 'phalcon',
            ]
        );
    }
);
return new Application($di);
