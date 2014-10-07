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
use vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestClassHandler;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\UserRepository;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType;

abstract class AbstractRadRestHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $handler;
    protected $container;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $resourceManager = new UserRepository();
        $resourceManager->setFakeUser(User::create(''));
        $this->container->set('resource_manager', $resourceManager);
        $this->container->set('form', new UserType());
        $this->container->set('form_factory', $this->getMock('Symfony\Component\Form\FormFactoryInterface'));
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

    public function testHandleGet()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users/{id}', 'get', 'GET');
        $reflMethod = $this->getReflectionMethod($route);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertSame(array('class'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', 'groups'=>array('Default')), $apiDoc->getOutput());
        $this->assertNull($apiDoc->getInput());
    }

    public function testHandleGetOverriddenMethod()
    {
        $apiDoc = new ApiDoc(array('resource'=>true));
        $route = $this->route('/users/{id}', 'get', 'GET', self::ROUTE_TYPE_OVERRIDDEN);
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

    public function testHandleUnsupported()
    {
        $apiDoc = new ApiDoc(array());
        $route = new Route('/', array('_controller'=>__METHOD__));
        $reflMethod = new \ReflectionMethod(__METHOD__);

        $this->handler->handle($apiDoc, array(), $route, $reflMethod);

        $this->assertNull($apiDoc->getInput());
        $this->assertNull($apiDoc->getOutput());
    }

    /**
     *
     * @param Route $route
     * @return \ReflectionMethod
     */
    abstract protected function getReflectionMethod(Route $route);

    /**
     *
     * @param int $type
     * @param string $action
     * @return string
     */
    abstract protected function getRouteControllerString($type, $action);

    private function route($path, $action, $method, $type = self::ROUTE_TYPE_DEFAULT)
    {
        $route = new Route($path, array('_controller'=>$this->getRouteControllerString($type, $action)));
        $route->setMethods($method);
        return $route;
    }
    const ROUTE_TYPE_DEFAULT = 1;
    const ROUTE_TYPE_OVERRIDDEN = 2;
}
