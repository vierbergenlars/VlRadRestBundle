<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

/**
 * Base Controller for Controllers using the RAD Rest functionality
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
abstract class RadRestController extends AbstractController implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    protected $container;

    protected function getRouteName($action)
    {
        // @codeCoverageIgnoreStart
        if($this->has('logger')) {
            $this->get('logger')->warning('It is recommended that you override '.__METHOD__.' in your own controllers. The standard implementation has a bad performance.', array('sourceController'=>get_class($this)));
        }
        // @codeCoverageIgnoreEnd

        $controller = get_class($this).'::'.$action.'Action';
        $routes     = $this->get('router')->getRouteCollection()->all();
        foreach($routes as $routeName => $route)
        {
            if($route->hasDefault('_controller')&&$route->getDefault('_controller') === $controller) {
                return $routeName;
            }
        }

        // @codeCoverageIgnoreStart
        throw new \LogicException('No route found for controller '.$controller);
        // @codeCoverageIgnoreEnd
    }

    protected function getPagination(PageDescriptionInterface $pageDescription, $page)
    {
        if($this->has('knp_paginator')) {
            return $this->get('knp_paginator')->paginate($pageDescription, $page);
        } else {
            return parent::getPagination($pageDescription, $page);
        }
    }

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool    true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }


}
