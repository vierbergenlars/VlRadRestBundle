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

/**
 * Resolves tagged services to a frontend manager
 */
class FrontendManagerTagsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $helper = new Helpers($container);
        $taggedResourceManagers      = $container->findTaggedServiceIds('radrest.resource_manager');

        foreach($taggedResourceManagers as $resourceManagerId => $tagAttributes) {
            foreach($tagAttributes as $attributes) {
                $resourceId             = $attributes['resource'];
                // Find the other services tagged with this resource id
                $formId                 = $helper->findTaggedServiceIdByAttributes('radrest.form', array('resource'=>$attributes['resource']));
                $authorizationCheckerId = $helper->findTaggedServiceIdByAttributes('radrest.authorization_checker', array('resource'=>$attributes['resource']));

                if($authorizationCheckerId === null) {
                    throw new \LogicException('There is no service tagged radrest.authorization_checker for resource "'.$resourceId.'"');
                }

                // Create a new definition for a frontend manager
                $definition = new Definition('vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager', array(
                    new Reference($resourceManagerId),
                    new Reference($authorizationCheckerId),
                    $formId===null?null:new Reference($formId),
                    new Reference('form.factory', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
                ));
                $definition->addTag('radrest.frontend_manager', array('resource'=>$resourceId));
                $container->setDefinition('radrest.frontend_manager.compiled.'.$resourceId, $definition);

                // Register an alias if possible
                $aliasBase = $this->findAliasBaseName(array($resourceManagerId, $authorizationCheckerId, $formId));
                if($aliasBase !== false) {
                    $helper->registerAliasIfNotExists($aliasBase.'.frontend_manager', 'radrest.frontend_manager.compiled.'.$resourceId);
                }
            }
        }
    }

    /**
     * Finds a common basename for all defined service ids
     * @param string[] $serviceIds
     * @return false|string
     */
    private function findAliasBaseName($serviceIds)
    {
        $pieces = explode('.', $serviceIds[0]);
        array_pop($pieces);
        $base = implode('.', $pieces);
        foreach($serviceIds as $id)
        {
            if($id === null) {
                continue;
            }
            $pieces = explode('.', $id);
            array_pop($pieces);
            if($base !== implode('.', $pieces)) {
                return false;
            }
        }

        return $base;
    }
}
