<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController;
use Symfony\Component\Routing\Route;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @coversNothing
 */
class ControllerServiceControllerIntegrationTest extends AbstractControllerIntegrationTest
{
    public function setUp()
    {
        parent::setUp();
        $this->container->register('acme.demo.user.controller')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserServiceController')
        ->setArguments(array(
            new Reference('resource_manager'),
            new Reference('form'),
            new Reference('form_factory'),
            null,
            new Reference('router'),
            'acme.demo.user.controller',
        ));
    }

    protected function route($path, $action, $method)
    {
        $route = new Route($path, array('_controller'=>'acme.demo.user.controller:'.$action.'Action'));
        $route->setMethods($method);
        return $route;
    }

    protected function createController()
    {
        return $this->container->get('acme.demo.user.controller');
    }

    /**
     * @expectedException LogicException
     */
    public function testRedirectToUnmetDependencies()
    {
        $this->container->getDefinition('acme.demo.user.controller')->setArguments(array(
            new Reference('resource_manager'),
            new Reference('form'),
            new Reference('form_factory'),
        ));
        $this->createController()->_redirectTo('cget');
    }
}
