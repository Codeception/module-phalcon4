<?php

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

        $module->_after($test);
    }

    public function testSession()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);
        $key = "phalcon";
        $value = "Rocks!";
        $module->haveInSession($key, $value);
        $module->seeInSession($key, $value);
        $module->seeSessionHasValues([$key => $value]);
        $module->_after($test);
    }

    public function testRecords()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);

        $module->haveRecord('App\Models\Articles', ['title' => 'phalcon']);
        $module->seeRecord('App\Models\Articles', ['title' => 'phalcon']);
        $module->seeNumberOfRecords('App\Models\Articles', 1);
        $module->haveRecord('App\Models\Articles', ['title' => 'phalcon']);
        $module->seeNumberOfRecords('App\Models\Articles', 2);
        $module->dontSeeRecord('App\Models\Articles', ['title' => 'wordpress']);

        $record = $module->grabRecord('App\Models\Articles', ['title' => 'phalcon']);
        $this->assertInstanceOf('Phalcon\Mvc\Model', $record);

        $module->_after($test);
    }

    public function testContainerMethods()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);

        $session = $module->grabServiceFromContainer('session');
        $this->assertInstanceOf('Codeception\Lib\Connector\Phalcon4\MemorySession', $session);

        $testService = $module->addServiceToContainer('std', function () {
            return new \stdClass();
        }, true);
        $this->assertInstanceOf('stdClass', $module->grabServiceFromContainer('std'));
        $this->assertInstanceOf('stdClass', $testService);
        $module->_after($test);
    }

    public function testRoutes()
    {
        $module = $this->getPhalconModule();
        $test = new Codeception\Test\Unit();
        $module->_before($test);

        $module->amOnRoute('front.index');
        $module->seeCurrentRouteIs('front.index');
        $module->_after($test);
    }
}
