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

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Router;

/**
 * This trait provides the default routing implementation for controllers
 */
trait DefaultRoutingTrait
{
    /**
     * @return LoggerInterface
     */
    abstract protected function getLogger();

    /**
     * @return Router
     */
    abstract protected function getRouter();

    /**
     * @param string $action
     * @return string
     */
    abstract protected function getActionResourceName($action);

    /**
     * Gets the name of the route for the given action on this controller.
     * @param string $action
     * @return string The route name
     */
    protected function getRouteName($action)
    {
        if($this->getLogger() !== null) {
            $this->getLogger()->warning('It is recommended that you override '.__METHOD__.' in your own controllers. The standard implementation has bad performance.', array('sourceController'=>get_class($this)));
        }

        $controller = $this->getActionResourceName($action);
        if(!$controller || !$this->getRouter()) {
            throw new \LogicException('To use the builtin method '.__METHOD__.', a router must be available.');
        }
        $routes = $this->getRouter()->getRouteCollection()->all();
        foreach($routes as $routeName => $route) {
            if($route->hasDefault('_controller')&&$route->getDefault('_controller') === $controller) {
                return $routeName;
            }
        }

        throw new \LogicException('No route found for controller '.$controller);
    }
}
