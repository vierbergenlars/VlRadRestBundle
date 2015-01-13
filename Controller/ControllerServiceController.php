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

use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultServiceRoutingTrait;

/**
 * Base class for RAD Rest service controllers
 */
class ControllerServiceController extends AbstractController
{
    use DefaultServiceRoutingTrait;

    /**
     * @var ResourceManagerInterface
     */
    private $resourceManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormTypeInterface
     */
    private $formType;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var Router|null
     */
    private $router;

    /**
     * @var string|null
     */
    private $serviceName;

    public function __construct(ResourceManagerInterface $resourceManager, FormTypeInterface $formType, FormFactoryInterface $formFactory, LoggerInterface $logger = null, RouterInterface $router = null, $serviceName = null)
    {
        $this->resourceManager = $resourceManager;
        $this->formType        = $formType;
        $this->formFactory     = $formFactory;
        $this->logger          = $logger;
        $this->router          = $router;
        $this->serviceName     = $serviceName;
    }

    public function getResourceManager()
    {
        return $this->resourceManager;
    }

    public function getFormType()
    {
        return $this->formType;
    }

    protected function getFormFactory()
    {
        return $this->formFactory;
    }

    protected function getLogger()
    {
        return $this->logger;
    }

    protected function getRouter()
    {
        return $this->router;
    }

    protected function getServiceName()
    {
        return $this->serviceName;
    }
}
