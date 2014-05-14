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

use Symfony\Component\Security\Core\Role\RoleHierarchy;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security\AuthorizationChecker;
use Symfony\Component\Security\Core\Role\Role;

class AbstractAuthorizationCheckerTest extends \PHPUnit_Framework_TestCase
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

		$this->trustResolver = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface');
		$this->roleHierarchy = new RoleHierarchy(array('ROLE_ADMIN'=>array('ROLE_USER', 'ROLE_SWITCH_USER'), 'ROLE_SUPER_ADMIN'=>array('ROLE_ADMIN')));

	}

	public function testBasicGetters()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver, $this->roleHierarchy);
		$this->assertSame($this->securityContext, $authorizationChecker->_getSecurityContext());
		$this->assertSame($this->trustResolver, $authorizationChecker->_getTrustResolver());
		$this->assertSame($this->roleHierarchy, $authorizationChecker->_getRoleHierarchy());
		$this->assertSame($this->token, $authorizationChecker->_getToken());
		$this->assertSame('abc123', $authorizationChecker->_getUser());
	}


	public function testNoToken()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue(null));
		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver);

		$this->assertNull($authorizationChecker->_getToken());
		$this->assertNull($authorizationChecker->_getUser());
		$this->assertSame(array(), $authorizationChecker->_getRoles());
		$this->assertFalse($authorizationChecker->_hasRole('ROLE_USER'));
	}

	public function testBasicGettersNullDependencies()
	{
		$authorizationChecker = new AuthorizationChecker();
		$this->assertNull($authorizationChecker->_getSecurityContext());
		$this->assertNull($authorizationChecker->_getTrustResolver());
		$this->assertNull($authorizationChecker->_getRoleHierarchy());
		$this->assertNull($authorizationChecker->_getToken());
		$this->assertNull($authorizationChecker->_getUser());
	}

	public function testRolesSingle()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver);
		$this->token->expects($this->once())->method('getRoles')->will($this->returnValue(array(new Role('ROLE_USER'))));
		$this->assertEquals(array(new Role('ROLE_USER')), $authorizationChecker->_getRoles());

	}

	public function testRolesMultiple()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver);
		$this->token->expects($this->once())->method('getRoles')->will($this->returnValue(array(new Role('ROLE_USER'), new Role('ROLE_ADMIN'))));
		$this->assertEquals(array(new Role('ROLE_USER'), new Role('ROLE_ADMIN')), $authorizationChecker->_getRoles());
	}

	public function testRolesHierarchy()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver, $this->roleHierarchy);
		$this->token->expects($this->once())->method('getRoles')->will($this->returnValue(array(new Role('ROLE_ADMIN'))));
		$returnedRoles = $authorizationChecker->_getRoles();
		$this->assertContains(new Role('ROLE_USER'), $returnedRoles, '', false, false);
		$this->assertContains(new Role('ROLE_SWITCH_USER'), $returnedRoles, '', false, false);
		$this->assertContains(new Role('ROLE_ADMIN'), $returnedRoles, '', false, false);
		$this->assertSame(3, count($returnedRoles));
	}

	public function testHasRole()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver);
		$this->token->expects($this->once())->method('getRoles')->will($this->returnValue(array(new Role('ROLE_USER'), new Role('ROLE_ADMIN'))));
		$this->assertTrue($authorizationChecker->_hasRole('ROLE_USER'));
		$this->assertTrue($authorizationChecker->_hasRole('ROLE_ADMIN'));
		$this->assertFalse($authorizationChecker->_hasRole('ROLE_SWITCH_USER'));
	}

	public function testHasRoleHierarchy()
	{
		$this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($this->token));

		$authorizationChecker = new AuthorizationChecker($this->securityContext, $this->trustResolver, $this->roleHierarchy);
		$this->token->expects($this->once())->method('getRoles')->will($this->returnValue(array(new Role('ROLE_USER'), new Role('ROLE_ADMIN'))));
		$this->assertTrue($authorizationChecker->_hasRole('ROLE_USER'));
		$this->assertTrue($authorizationChecker->_hasRole('ROLE_ADMIN'));
		$this->assertTrue($authorizationChecker->_hasRole('ROLE_SWITCH_USER'));
		$this->assertFalse($authorizationChecker->_hasRole('ROLE_SUPER_ADMIN'));
	}
}
