<?php

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use Symfony\Component\DependencyInjection\Container;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserServiceController;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController
 */
class ControllerServiceControllerTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;
    private $formType;
    private $formFactory;
    private $logger;
    private $router;
    private $serviceName;

    protected function setUp()
    {
        $this->resourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');
        $this->formType = $this->getMock('Symfony\Component\Form\FormTypeInterface');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->serviceName = 'app.user.controller';
    }

    public function testGetLogger()
    {
        $controller = new UserServiceController(
            $this->resourceManager,
            $this->formType,
            $this->formFactory,
            $this->logger,
            $this->router,
            $this->serviceName
        );
        $this->assertSame($this->logger, $controller->_getLogger());
    }

    public function testGetLoggerNull()
    {
        $controller = new UserServiceController(
            $this->resourceManager,
            $this->formType,
            $this->formFactory,
            null,
            $this->router,
            $this->serviceName
        );

        $this->assertNull($controller->_getLogger());
    }

    public function testGetRouter()
    {
        $controller = new UserServiceController(
            $this->resourceManager,
            $this->formType,
            $this->formFactory,
            $this->logger,
            $this->router,
            $this->serviceName
        );

        $this->assertSame($this->router, $controller->_getRouter());
    }

    public function testGetRouterNull()
    {
        $controller = new UserServiceController(
            $this->resourceManager,
            $this->formType,
            $this->formFactory,
            $this->logger,
            null,
            $this->serviceName
        );

        $this->assertNull($controller->_getRouter());
    }

    public function testGetFormFactory()
    {
        $controller = new UserServiceController(
            $this->resourceManager,
            $this->formType,
            $this->formFactory,
            $this->logger,
            $this->router,
            $this->serviceName
        );

        $this->assertSame($this->formFactory, $controller->_getFormFactory());
    }

    public function testGetServiceName()
    {
        $controller = new UserServiceController(
            $this->resourceManager,
            $this->formType,
            $this->formFactory,
            $this->logger,
            $this->router,
            $this->serviceName
        );

        $this->assertSame($this->serviceName, $controller->_getServiceName());
    }
}