<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Routes;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\View\View;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Pagination\ArrayPageDescription;
use FOS\RestBundle\Util\Codes;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\EditTrait
 * @covers vierbergenlars\Bundle\RadRestBundle\View\View
 */
class EditTraitTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;

    private $editTrait;

    private $form;

    private $user;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->resourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');

        $this->editTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\EditTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->editTrait->expects($this->once())
            ->method('getResourceManager')
            ->with()
            ->willReturn($this->resourceManager);

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->editTrait->expects($this->once())
            ->method('createForm')
            ->with($this->anything(), 'PUT')
            ->willReturn($this->form);

        $this->editTrait->expects($this->once())
            ->method('handleView')
            ->with($this->anything())
            ->willReturnArgument(0);

        $this->resourceManager->expects($this->once())
            ->method('find')
            ->with(8)
            ->willReturn($this->user = User::create('abc', 8));

    }

    public function testEdit()
    {
        $view = $this->editTrait->editAction(8);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($this->form, $view->getData());
    }

    public function testPutFail()
    {
        $request = new Request();
        $request->setMethod('PUT');

        $this->editTrait->expects($this->once())
            ->method('processForm')
            ->with($this->form, $request)
            ->willReturn(false);

        $view = $this->editTrait->putAction($request, 8);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($this->form, $view->getData());
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $view->getStatusCode());
    }

    public function testPutSuccess()
    {
        $request = new Request();
        $request->setMethod('PUT');

        $this->editTrait->expects($this->once())
            ->method('processForm')
            ->with($this->form, $request)
            ->willReturn(true);

        $this->editTrait->expects($this->once())
            ->method('redirectTo')
            ->with('get', array('id'=>8))
            ->willReturn($redirect = View::createRouteRedirect('get_user', array('id'=>8)));

        $view = $this->editTrait->putAction($request, 8);

        $this->assertSame($redirect, $view);
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $view->getStatusCode());
    }
}
