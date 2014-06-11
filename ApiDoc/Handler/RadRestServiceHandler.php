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

class RadRestServiceHandler extends AbstractRadRestHandler
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function isSupported(\ReflectionMethod $reflMethod)
    {
        return $reflMethod->getDeclaringClass()->getName() === 'vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController';
    }

    protected function getControllerInstance(Route $route)
    {
        $controller       = $route->getDefault('_controller');
        $controllerPieces = explode(':', $controller);
        $controllerService  = $controllerPieces[0];

        return $this->container->get($controllerService);
    }
}
