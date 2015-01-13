<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Manager;

use vierbergenlars\Bundle\RadRestBundle\Manager\SecuredResourceManager;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Manager\SecuredResourceManager
 */
class SecuredResourceManagerTest extends \PHPUnit_Framework_TestCase
{
    private $authorizationChecker;
    private $resourceManager;
    private $wrappedResourceManager;

    protected function setUp()
    {
        $this->authorizationChecker = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface');
        $this->wrappedResourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');
        $this->resourceManager = new SecuredResourceManager($this->wrappedResourceManager, $this->authorizationChecker);
    }

    public function authorizationProvider()
    {
        return array(
            array('getPageDescription', 'mayList', true, true),
            array('getPageDescription', 'mayList', false, false),
            array('find', 'mayView', true, true),
            array('find', 'mayView', false, true),
            array('create', 'mayCreate', true, true),
            array('create', 'mayCreate', false, false),
            array('update', 'mayEdit', true, true),
            array('update', 'mayEdit', false, false),
            array('delete', 'mayDelete', true, true),
            array('delete', 'mayDelete', false, false),
        );
    }

    /**
     * @dataProvider authorizationProvider
     */
    public function testAuthorizationChecks($method, $authMethod, $allowed, $willCallWrappedManager)
    {
        $this->authorizationChecker->expects($this->once())
            ->method($authMethod)
            ->willReturn($allowed);
        $this->wrappedResourceManager->expects($willCallWrappedManager?$this->once():$this->never())
            ->method($method)
            ->willReturn($ret = new \stdClass());
        if(!$allowed) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }
        $this->assertSame($ret, $this->resourceManager->{$method}(new \stdClass()));
    }

    public function testNewInstance()
    {
        $this->wrappedResourceManager->expects($this->once())
            ->method('newInstance')
            ->willReturn($ret = new \stdClass());

        $this->assertSame($ret, $this->resourceManager->newInstance());
    }

    public function testAccessorMethods()
    {
        $this->assertSame($this->wrappedResourceManager, $this->resourceManager->getResourceManager());
        $this->assertSame($this->authorizationChecker, $this->resourceManager->getAuthorizationChecker());
    }
}
