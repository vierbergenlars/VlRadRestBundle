<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Twig;

use vierbergenlars\Bundle\RadRestBundle\Twig\ControllerVariables;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Twig\ControllerVariables
 */
class ControllerVariablesTest extends \PHPUnit_Framework_TestCase
{
    private $controllerVariables;
    private $controller;

    protected function setUp()
    {
        $this->controller = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface');
        $this->controllerVariables = new ControllerVariables($this->controller);
    }

    public function testRoute()
    {
        $this->controller->expects($this->once())
            ->method('getRouteName')
            ->with('cget')
            ->willReturn('get_users');

        $this->assertSame($this->controllerVariables->route('cget'), 'get_users');
    }

    private function getResourceManager($authorizationChecker)
    {
        $rm = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Manager\SecuredResourceManager')
            ->disableOriginalConstructor()
            ->getMock();
        $rm->expects($this->atLeastOnce())
            ->method('getAuthorizationChecker')
            ->willReturn($authorizationChecker);
        return $rm;
    }

    public function testGetAuthorizationChecker()
    {
        $authorizationChecker = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface');
        $resourceManager = $this->getResourceManager($authorizationChecker);

        $this->controller->expects($this->once())
            ->method('getResourceManager')
            ->willReturn($resourceManager);

        $this->assertSame($this->controllerVariables->getAuthorizationChecker(), $authorizationChecker);
    }

    /**
     * @dataProvider mayProvider
     */
    public function testMay($action, $object, $calledMethod)
    {
        $authorizationChecker = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface');
        $resourceManager = $this->getResourceManager($authorizationChecker);

        $this->controller->expects($this->once())
            ->method('getResourceManager')
            ->willReturn($resourceManager);

        if(!$object) {
            $authorizationChecker->expects($this->once())
                ->method($calledMethod)
                ->with()
                ->willReturn(true);
        } else {
            $authorizationChecker->expects($this->once())
                ->method($calledMethod)
                ->with($object)
                ->willReturn(true);
        }

        $this->assertSame($this->controllerVariables->may($action, $object), true);
    }

    public static function mayProvider()
    {
        return array(
            array('cget', null, 'mayList'),
            array('list', null, 'mayList'),
            array('get', new \stdClass(), 'mayView'),
            array('view', new \stdClass(), 'mayView'),
            array('new', new \stdClass(), 'mayCreate'),
            array('post', new \stdClass(), 'mayCreate'),
            array('create', new \stdClass(), 'mayCreate'),
            array('edit', new \stdClass(), 'mayEdit'),
            array('put', new \stdClass(), 'mayEdit'),
            array('remove', new \stdClass(), 'mayDelete'),
            array('delete', new \stdClass(), 'mayDelete'),
        );
    }
}
