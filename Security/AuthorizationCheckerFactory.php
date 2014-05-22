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
 * A factory for all subclasses of {@link AbstractAuthorizationChecker}
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class AuthorizationCheckerFactory
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

    public function __construct(SecurityContextInterface $context = null, AuthenticationTrustResolverInterface $trustResolver = null, RoleHierarchyInterface $roleHierarchy = null)
    {
        $this->context = $context;
        $this->trustResolver = $trustResolver;
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Creates a new authorization checker
     *
     * @param string $class
     *            The class to instanciate
     * @throws \LogicException
     * @return AbstractAuthorizationChecker
     */
    public function createChecker($class)
    {
        if (! is_subclass_of($class, __NAMESPACE__ . '\\AbstractAuthorizationChecker')) {
            throw new \LogicException(sprintf('%s is not a subclass of %s', $class, __NAMESPACE__ . '\\AbstractAuthorizationChecker'));
        }
        return new $class($this->context, $this->trustResolver, $this->roleHierarchy);
    }
}
