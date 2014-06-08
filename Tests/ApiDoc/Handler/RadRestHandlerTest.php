<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\ApiDoc\Handler;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Route;
use vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestHandler;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\UserRepository;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestHandler
 */
class RadRestHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $handler;

    public function setUp()
    {
        $container = new ContainerBuilder();
        $container->set('frontend_manager', $this->getFrontendManager());

        $this->handler = new RadRestHandler($container);
    }

    public function testHandleCGet()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users', 'cget', 'GET');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame(array('class'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', 'groups'=>array('abc', 'def')), $apiDoc->getOutput());
        $this->assertNull($apiDoc->getInput());
    }

    public function testHandleCGetDefaultSerialization()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users', 'cget', 'GET', 'SwitchedSerializationController');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame(array('class'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', 'groups'=>array('Default')), $apiDoc->getOutput());
        $this->assertNull($apiDoc->getInput());
    }

    public function testHandleGet()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users/{id}', 'get', 'GET');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame(array('class'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', 'groups'=>array('Default')), $apiDoc->getOutput());
        $this->assertNull($apiDoc->getInput());
    }

    public function testHandleGetCustomSerialization()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users/{id}', 'get', 'GET', 'SwitchedSerializationController');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame(array('class'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', 'groups'=>array('abc', 'def')), $apiDoc->getOutput());
        $this->assertNull($apiDoc->getInput());
    }

    public function testHandleGetOverriddenMethod()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users/{id}', 'get', 'GET', 'OverriddenMethodController');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertNull($apiDoc->getInput());
        $this->assertNull($apiDoc->getOutput());
    }

    public function testHandlePost()
    {
        $apiDoc = new ApiDoc(array());
        $route = $this->route('/users', 'post', 'POST');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType', $apiDoc->getInput());
        $this->assertNull($apiDoc->getOutput());
    }

    public function testHandlePut()
    {
        $apiDoc = new ApiDoc(array());
        $route = $this->route('/users/{id}', 'put', 'PUT');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType', $apiDoc->getInput());
        $this->assertNull($apiDoc->getOutput());
    }

    public function testHandlePatch()
    {
        $apiDoc = new ApiDoc(array());
        $route = $this->route('/users/{id}', 'patch', 'PATCH');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType', $apiDoc->getInput());
        $this->assertNull($apiDoc->getOutput());
    }

    public function testHandleDelete()
    {
        $apiDoc = new ApiDoc(array());
        $route = $this->route('/users/{id}', 'delete', 'DELETE');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertNull($apiDoc->getInput());
        $this->assertNull($apiDoc->getOutput());
    }


    private function getReflectionMethod(Route $route)
    {
        return new \ReflectionMethod($route->getDefault('_controller'));
    }

    private function route($path, $action, $method = null, $class = 'UserController')
    {
        $route = new Route($path, array('_controller'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\\'.$class.'::'.$action.'Action'));
        $route->setMethods($method);
        return $route;
    }

    private function getFrontendManager()
    {
        $resourceManager = new UserRepository();
        $resourceManager->fakeUser = User::create('');
        $authorizationChecker = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface');
        $form = new UserType();
        return new FrontendManager($resourceManager, $authorizationChecker, $form);
    }

}
