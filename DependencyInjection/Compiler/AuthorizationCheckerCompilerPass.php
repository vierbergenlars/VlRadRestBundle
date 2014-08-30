<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AuthorizationCheckerCompilerPass implements CompilerPassInterface
{
    const SECURITY_CONTEXT_INTERFACE = 'Symfony\Component\Security\Core\SecurityContextInterface';
    const SECURITY_CONTEXT_SERVICE   = 'security.context';
    const TRUST_RESOLVER_INTERFACE   = 'Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface';
    const TRUST_RESOLVER_SERVICE     = 'security.authentication.trust_resolver';
    const ROLE_HIERARCHY_INTERFACE   = 'Symfony\Component\Security\Core\Role\RoleHierarchyInterface';
    const ROLE_HIERARCHY_SERVICE     = 'security.role_hierarchy';

    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('radrest.authorization_checker');
        foreach($serviceIds as $serviceId => $tagAttributes)
        {
            foreach($tagAttributes as $attribute)
            {
                $serviceDefinition = $container->findDefinition($serviceId);
                $this->processService($serviceDefinition);
            }
        }
    }

    protected function processService(Definition $definition)
    {
        $reflectionClass = new \ReflectionClass($definition->getClass());
        $constructor     = $reflectionClass->getConstructor();
        $parameters      = $constructor->getParameters();
        $arguments       = $definition->getArguments();

        foreach($parameters as $parameter) {
            $typeHint = $parameter->getClass();
            if($typeHint !== null) {
                switch($typeHint->name) {
                    case static::SECURITY_CONTEXT_INTERFACE:
                        array_splice($arguments, $parameter->getPosition(), 0, array(new Reference(static::SECURITY_CONTEXT_SERVICE, ContainerInterface::NULL_ON_INVALID_REFERENCE)));
                        break;
                    case static::TRUST_RESOLVER_INTERFACE:
                        array_splice($arguments, $parameter->getPosition(), 0, array(new Reference(static::TRUST_RESOLVER_SERVICE, ContainerInterface::NULL_ON_INVALID_REFERENCE)));
                        break;
                    case static::ROLE_HIERARCHY_INTERFACE:
                        array_splice($arguments, $parameter->getPosition(), 0, array(new Reference(static::ROLE_HIERARCHY_SERVICE, ContainerInterface::NULL_ON_INVALID_REFERENCE)));
                        break;
                }
            }
        }

        $definition->setArguments($arguments);
    }
}
