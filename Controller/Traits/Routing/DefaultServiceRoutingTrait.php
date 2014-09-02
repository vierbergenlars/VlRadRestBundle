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
 * This trait provides the default routing implementation for controllers registered as a service
 */
trait DefaultServiceRoutingTrait
{
    use DefaultRoutingTrait;

    /**
     * Gets the service name the controller is registered with in the router
     * @return string
     */
    abstract protected function getServiceName();

    protected function getActionResourceName($action)
    {
        return $this->getServiceName().':'.$action.'Action';
    }
}
