<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing;

use Symfony\Component\DependencyInjection\Container;

/**
 * This trait provides a routing implementation based on the route name generation algorithm of the FOS RestBundle.
 * However, it may not work in all cases.
 */
trait ClassResourceRoutingTrait
{
    public function getRouteName($action)
    {
        static $cache = array();
        if(!isset($cache[$action])) {
            $parts = explode('\\',  get_class($this));
            $className = array_pop($parts);
            $baseName = substr($className, 0, -10);
            $routeBaseName = Container::underscore($baseName);
            if($action === 'cget') {
                $cache[$action] = 'get_'.$routeBaseName.'s';
            } else {
                $cache[$action] = $action.'_'.$routeBaseName;
            }
        }
        return $cache[$action];
    }
}
