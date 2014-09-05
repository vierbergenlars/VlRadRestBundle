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

/**
 * This trait provides the default routing implementation for class-based controllers
 */
trait DefaultClassRoutingTrait
{
    use DefaultRoutingTrait;

    protected function getActionResourceName($action)
    {
        return get_class($this).'::'.$action.'Action';
    }
}
