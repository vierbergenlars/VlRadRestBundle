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

/**
 * Converts services tagged with radrest.entity_repository to the entity repository belonging to the class set in the service.
 *
 * Entity repositories are automatically retrieved from the default entity manager `doctrine.orm.entity_manager`.
 * The entity manager can be changed by adding a tag attribute `entity_manager` with the service to use as the entity manager for that repository.
 */
class EntityRepositoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('radrest.entity_repository');
        foreach($serviceIds as $serviceId => $tagAttributes)
        {
            foreach($tagAttributes as $attribute)
            {
                $serviceDefinition = $container->findDefinition($serviceId);
                if(!isset($attribute['entity_manager'])) {
                    $attribute['entity_manager'] = 'doctrine.orm.entity_manager';
                }
                $this->processService($serviceDefinition, $attribute['entity_manager']);
            }
        }
    }

    protected function processService(Definition $definition, $entityManager)
    {
        $definition->setFactoryService($entityManager);
        $definition->setFactoryMethod('getRepository');
        $definition->setArguments(array($definition->getClass()));
        $definition->setClass('Doctrine\ORM\EntityRepository');
    }
}
