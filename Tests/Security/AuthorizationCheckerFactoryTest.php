<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Security;

use vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerFactory;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerFactory
 */
class AuthorizationCheckerFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $securityContext;
    private $trustResolver;
    private $roleHierarchy;
    private $token;

    public function setUp()
    {
        $this->token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->token->expects($this->any())->method('getUser')->will($this->returnValue('abc123'));
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

        $this->trustResolver = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface');
        $this->roleHierarchy = new RoleHierarchy(array('ROLE_ADMIN'=>array('ROLE_USER', 'ROLE_SWITCH_USER'), 'ROLE_SUPER_ADMIN'=>array('ROLE_ADMIN')));
    }

    public function testCreate()
    {
        $factory = new AuthorizationCheckerFactory($this->securityContext, $this->trustResolver, $this->roleHierarchy);
        $authorizationChecker = $factory->createChecker('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security\AuthorizationChecker');

        $this->assertInstanceOf('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security\AuthorizationChecker', $authorizationChecker);
        $this->assertSame($this->securityContext, $authorizationChecker->_getSecurityContext());
        $this->assertSame($this->trustResolver, $authorizationChecker->_getTrustResolver());
        $this->assertSame($this->roleHierarchy, $authorizationChecker->_getRoleHierarchy());
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateInvalidClass()
    {
        $factory = new AuthorizationCheckerFactory($this->securityContext, $this->trustResolver, $this->roleHierarchy);
        $authorizationChecker = $factory->createChecker('stdObject');

    }
}
