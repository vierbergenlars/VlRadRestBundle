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
use Symfony\Component\DependencyInjection\Definition;

class AuthorizationCheckerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('radrest.authorization_checker');
        foreach($serviceIds as $serviceId => $tagAttributes)
        {
            foreach($tagAttributes as $attribute)
            {
                $serviceDefinition = $container->findDefinition($serviceId);
                if(!isset($attribute['factory']) || $attribute['factory'] === true) {
                    $this->processService($serviceDefinition);
                }
            }
        }
    }

    private function processService(Definition $definition)
    {
        if($definition->getFactoryService() === null&&$definition->getFactoryClass() === null) {
            $definition->setFactoryService('radrest.authorization_checker.factory');
            $definition->setFactoryMethod('createChecker');
            $definition->setArguments(array($definition->getClass()));
        }

    }
}
