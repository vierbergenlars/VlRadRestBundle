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

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Router;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;
use Knp\Component\Pager\Paginator;

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
     * @var Paginator|null
     */
    private $paginator;

    /**
     *
     * @param FrontendManager $frontendManager The frontend manager for this resource
     * @param LoggerInterface|null $logger
     * @param Router|null $router Required only when using the default redirectTo() method
     * @param string|null $serviceName Required only when using the default redirectTo() method
     */
    public function __construct(FrontendManager $frontendManager, LoggerInterface $logger = null, Router $router = null, $serviceName = null)
    {
        $this->frontendManager = $frontendManager;
        $this->logger          = $logger;
        $this->router          = $router;
        $this->serviceName     = $serviceName;
    }

    public function setPaginator(Paginator $paginator) {
        $this->paginator = $paginator;
    }

    public function getFrontendManager()
    {
        return $this->frontendManager;
    }

    protected function getRouteName($action)
    {
        // @codeCoverageIgnoreStart
        if($this->logger !== null) {
            $this->logger->warning('It is recommended that you override '.__METHOD__.' in your own controllers. The standard implementation has bad performance.', array('sourceController'=>get_class($this)));
        }
        // @codeCoverageIgnoreEnd

        if($this->serviceName === null || $this->router === null) {
            throw new \LogicException('To use the builtin method '.__METHOD__.', the router and service name must be injected during construction.');
        }
        $controller = $this->serviceName.':'.$action.'Action';
        $routes     = $this->router->getRouteCollection()->all();
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
        if($this->paginator instanceof Paginator) {
            return $this->paginator->paginate($pageDescription, $page);
        } else {
            return parent::getPagination($pageDescription, $page);
        }
    }
}
