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

use FOS\RestBundle\Controller\Annotations\View as AView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base Controller for Controllers using the RAD Rest functionality
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class RadRestController extends AbstractController implements ContainerAwareInterface
{
    private $frontendManager;
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function setFrontendManager(FrontendManager $frontendManager)
    {
        $this->frontendManager = $frontendManager;
    }

    public function getFrontendManager()
    {
        return $this->frontendManager;
    }

    protected function handleView(View $view)
    {
        return $view;
    }

    protected function redirectTo($nextAction, array $params = array())
    {
        $controller = get_class($this).'::'.$nextAction.'Action';
        $routes     = $this->get('router')->getRouteCollection()->all();
        // FIXME: Get rid of O(n) performance on routes
        foreach($routes as $routeName => $route)
        {
            if($route->hasDefault('_controller')&&$route->getDefault('_controller') === $controller) {
                return View::createRouteRedirect($routeName, $params);
            }
        }

        // @codeCoverageIgnoreStart
        throw new \LogicException('No route found for controller '.$controller);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns a list of serializer groups for each type of GET request (list & single object view)
     * @codeCoverageIgnore
     * @return array<string, string[]>
     */
    public function getSerializationGroups()
    {
        return array();
    }
}
