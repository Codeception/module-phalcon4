<?php

use Codeception\Util\Autoload;
use Codeception\Module\Phalcon4;
use Codeception\Exception\ModuleConfigException;

class Phalcon4ModuleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _setUp()
    {
        Autoload::addNamespace('Codeception\Module', BASE_PATH . '/src/Codeception/Module');
        Autoload::addNamespace('Codeception\Lib\Connector\Phalcon4', BASE_PATH . '/src/Codeception/Lib/Connector/Phalcon4');
        require_once BASE_PATH . '/src/Codeception/Lib/Connector/Phalcon4.php';
        require_once BASE_PATH . '/src/Codeception/Lib/Connector/Phalcon4/MemorySession.php';
    }

    protected function _before()
    {

    }

    protected function _after()
    {
    }

    protected function getPhalconModule()
    {
        $container = \Codeception\Util\Stub::make('Codeception\Lib\ModuleContainer');
        $module = new Phalcon4($container);
        $module->_setConfig([
            'bootstrap'  => 'tests/_data/bootstrap.php',
            'cleanup'    => true,
            'savepoints' => true,
            'session'    => 'Codeception\Lib\Connector\Phalcon4\MemorySession'
        ]);
        $module->_initialize();
        return $module;
    }

    protected function getPhalconModuleMicro()
    {
        $container = \Codeception\Util\Stub::make('Codeception\Lib\ModuleContainer');
        $module = new Phalcon4($container);
        $module->_setConfig([
            'bootstrap'  => 'tests/_data/bootstrap-micro.php',
            'cleanup'    => true,
            'savepoints' => true,
            'session'    => PhalconConnector\MemorySession::class
        ]);
        $module->_initialize();
        return $module;
    }

    public function testConstruct()
    {
        $container = \Codeception\Util\Stub::make('Codeception\Lib\ModuleContainer');
        $module = new Phalcon4($container);
        $this->assertInstanceOf('Codeception\Module\Phalcon4', $module);
    }

    public function testInitialize()
    {
        $module = $this->getPhalconModule();
        $this->assertInstanceOf('Codeception\Lib\Connector\Phalcon4', $module->client);
    }

    public function testBefore()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);
        $this->assertInstanceOf('Phalcon\Di', $module->di);
        $this->assertInstanceOf('Phalcon\Di', $module->di);
    }

    public function testAfter()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);
        $module->_after($test);
        $this->assertNull($module->di);
    }

    public function testParts()
    {
        $module = $this->getPhalconModule();
        $this->assertEquals(['orm', 'services'], $module->_parts());
    }

    public function testGetApplication()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);
        $this->assertInstanceOf('Phalcon\Mvc\Application', $module->getApplication());

        $module = $this->getPhalconModuleMicro();
        $test = new Codeception\Test\Unit();
        $module->_before($test);
        $this->assertInstanceOf('Phalcon\Mvc\Micro', $module->getApplication());
    }

    public function testSession()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);
        $key = "phalcon";
        $value = "Rocks!";
        $module->haveInSession($key, $value);
        $module->seeInSession($key, $value );
        $module->seeSessionHasValues([$key => $value]);
    }

    public function testRecords()
    {
        require_once codecept_data_dir('models/test.php');

        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);

        $module->haveRecord('Test', ['name' => 'phalcon']);
        $module->seeRecord('Test', ['name' => 'phalcon']);
        $module->seeNumberOfRecords('Test', 1);
        $module->haveRecord('Test', ['name' => 'phalcon']);
        $module->seeNumberOfRecords('Test', 2);
        $module->dontSeeRecord('Test', ['name' => 'wordpress']);

        $record = $module->grabRecord('Test', ['name' => 'phalcon']);
        $this->assertInstanceOf('Phalcon\Mvc\Model', $record);
    }

    public function testContainerMethods()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);

        $session = $module->grabServiceFromContainer('session');
        $this->assertInstanceOf('session', $session);
    }
}
