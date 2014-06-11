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

use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as AView;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Routing\Router;
use Psr\Log\LoggerInterface;

/**
 * Base class for RAD Rest service controllers
 */
class ControllerServiceController extends AbstractController
{
    /**
     * @var FrontendManager
     */
    private $frontendManager;

    /**
     *
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * Required only when using the default redirectTo() method
     * @var Router|null
     */
    private $router;

    /**
     * Required only when using the default redirectTo() method
     * @var string|null
     */
    private $serviceName;

    /**
     *
     * @param FrontendManager $frontendManager The frontend manager for this resource
     * @param LoggerInterface $logger
     * @param Router $router Required only when using the default redirectTo() method
     * @param string $serviceName Required only when using the default redirectTo() method
     */
    public function __construct(FrontendManager $frontendManager, LoggerInterface $logger = null, Router $router = null, $serviceName = null)
    {
        $this->frontendManager = $frontendManager;
        $this->logger          = $logger;
        $this->router          = $router;
        $this->serviceName     = $serviceName;
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
        // @codeCoverageIgnoreStart
        if($this->logger !== null) {
            $this->logger->warning('It is recommended that you override '.__METHOD__.' in your own controllers. The standard implementation has bad performance.', array('sourceController'=>get_class($this)));
        }
        // @codeCoverageIgnoreEnd

        if($this->serviceName === null || $this->router === null) {
            throw new \LogicException('To use the builtin method '.__METHOD__.', the router and service name must be injected during construction.');
        }
        $controller = $this->serviceName.':'.$nextAction.'Action';
        $routes     = $this->router->getRouteCollection()->all();
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
