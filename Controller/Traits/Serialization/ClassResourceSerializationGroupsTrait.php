<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization;

use Symfony\Component\DependencyInjection\Container;

/**
 * This trait provides an implementation of serialization groups based on the controller name
 *
 * For resource lists (cget): groups 'list' and <controller_name>.'_list'
 * For resource pages (get) : groups 'object' and <controller_name>.'_object'
 */
trait ClassResourceSerializationGroupsTrait
{
    public function getSerializationGroups($action)
    {
        $parts = explode('\\',  get_class($this));
        $className = array_pop($parts);
        $baseName = substr($className, 0, -10);
        $routeBaseName = Container::underscore($baseName);

        switch($action) {
            case 'cget':
                return array('list', $routeBaseName.'_list');
            case 'get':
                return array('object', $routeBaseName.'_object');
        }
    }
}
