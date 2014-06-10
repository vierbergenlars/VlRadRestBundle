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

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Helpers
{
    /**
     *
     * @var ContainerBuilder
     */
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Finds a service which is tagged with a specific attributes
     * @param string $name Tag name
     * @param array $targetAttributes Attributes the tag must contain
     * @return string|null
     */
    public function findTaggedServiceIdByAttributes($name, $targetAttributes)
    {
        $taggedServices = $this->container->findTaggedServiceIds($name);
        foreach($taggedServices as $id => $tagAttributes) {
            foreach($tagAttributes as $attributes) {
                if(array_diff_assoc($targetAttributes, $attributes) === array()) {
                    return $id;
                }
            }
        }
        return null;
    }

    /**
     * Registers an alias if it does not exist yet.
     * @param string $alias
     * @param string $id
     * @return boolean
     */
    public function registerAliasIfNotExists($alias, $id)
    {
        if($this->container->has($alias)) {
            return false;
        }

        $this->container->setAlias($alias, $id);

        return true;
    }
}
