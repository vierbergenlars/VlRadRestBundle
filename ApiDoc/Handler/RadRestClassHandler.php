<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler;

use Symfony\Component\Routing\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RadRestClassHandler extends AbstractRadRestHandler
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function isSupported(Route $route)
    {
        return strpos($route->getDefault('_controller'), '::') !== false;
    }

    protected function getControllerInstance(Route $route)
    {
        $controller       = $route->getDefault('_controller');
        $controllerPieces = explode('::', $controller);
        $controllerClass  = $controllerPieces[0];

        $controllerInst = new $controllerClass(); // Must be RadRestController, because the method we got came from there
        $controllerInst->setContainer($this->container);
        return $controllerInst;
    }
}
