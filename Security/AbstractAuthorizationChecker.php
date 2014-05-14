<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Security;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Abstract baseclass for authorization checkers that contains commonly used functions
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
abstract class AbstractAuthorizationChecker implements AuthorizationCheckerInterface
{

	/**
	 *
	 * @var SecurityContextInterface
	 */
	private $context;

	/**
	 *
	 * @var AuthenticationTrustResolverInterface
	 */
	private $trustResolver;

	/**
	 *
	 * @var RoleHierarchyInterface
	 */
	private $roleHierarchy;

	/**
	 * Cache for roles that have been converted to strings
	 *
	 * @var array
	 */
	private $_cached_role_strings;

	final public function __construct(SecurityContextInterface $context = null, AuthenticationTrustResolverInterface $trustResolver = null, RoleHierarchyInterface $roleHierarchy = null)
	{
		$this->context = $context;
		$this->trustResolver = $trustResolver;
		$this->roleHierarchy = $roleHierarchy;
	}

	/**
	 *
	 * @return SecurityContextInterface|null
	 */
	protected function getSecurityContext()
	{
		return $this->context;
	}

	/**
	 *
	 * @return AuthenticationTrustResolverInterface|null
	 */
	protected function getTrustResolver()
	{
		return $this->trustResolver;
	}

	/**
	 *
	 * @return RoleHierarchyInterface|null
	 */
	protected function getRoleHierarchy()
	{
		return $this->roleHierarchy;
	}

	/**
	 *
	 * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface|null
	 */
	protected function getToken()
	{
		if(($securityContext = $this->getSecurityContext())) {
			return $securityContext->getToken();
		}
		return null;
	}

	/**
	 *
	 * @return \Symfony\Component\Security\Core\Role\RoleInterface[]
	 */
	protected function getRoles()
	{
		if(($token = $this->getToken())) {
			$roles = $token->getRoles();

			if(($roleHierarchy = $this->getRoleHierarchy())) {
				return $roleHierarchy->getReachableRoles($roles);
			}

			return $roles;
		}

		return array();
	}

	/**
	 *
	 * @return UserInterface|string|null
	 */
	protected function getUser()
	{
		if(($token = $this->getToken())) {
			return $token->getUser();
		}

		return null;
	}

	/**
	 *
	 * @param string $role
	 * @return boolean
	 */
	protected function hasRole($role)
	{
		if (! $this->_cached_role_strings) {
			$this->_cached_role_strings = array_map(function ($role)
			{
				return $role->getRole();
			}, $this->getRoles());
		}
		return in_array($role, $this->_cached_role_strings);
	}
}
