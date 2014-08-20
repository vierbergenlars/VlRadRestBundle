<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\PageableUserRepository;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController
 * @covers vierbergenlars\Bundle\RadRestBundle\Pagination\Adapters\KnpPaginationSubscriber
 */
class KnpPaginationControllerServiceControllerTest extends ControllerServiceControllerTest
{
    public function setUp()
    {
        parent::setUp();
        $this->registerKnpPaginatorService();
    }

    protected function createFrontendManagerArgs() {
        $this->resourceManager = new PageableUserRepository();
        $this->resourceManager->fakeUsers = User::createArray(23);
        return parent::createFrontendManagerArgs();
    }

    private function fireKernelRequestEventOnPagination(Request $request)
    {
        $this->container->get('knp_paginator.subscriber.sliding_pagination')
            ->onKernelRequest(new GetResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST));
    }

    public function testCGet()
    {
        $controller = $this->createController();

        $request = new Request();
        $request->attributes->set('_route', 'get_users');

        $this->fireKernelRequestEventOnPagination($request);
        $retval = $controller->cgetAction($request);
        $this->assertSame(array_slice($this->resourceManager->fakeUsers, 0, 10), $retval->getData()->getItems());
        $this->assertSame(23, $retval->getData()->getTotalItemCount());
        $this->assertSame('data', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
        $this->assertSame(array('abc', 'def'), $retval->getSerializationContext()->attributes->get('groups')->get());

        $request->query->set('page', 2);
        $this->fireKernelRequestEventOnPagination($request);
        $retval = $controller->cgetAction($request);

        $this->assertSame(array_slice($this->resourceManager->fakeUsers, 10, 10), $retval->getData()->getItems());
    }
}
