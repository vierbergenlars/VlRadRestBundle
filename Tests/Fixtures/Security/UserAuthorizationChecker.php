<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security;

use vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;

abstract class UserAuthorizationChecker implements AuthorizationCheckerInterface
{
    public function __construct(RoleHierarchyInterface $rh, SecurityContextInterface $sc, AuthenticationTrustResolverInterface $tr)
    {

    }
}