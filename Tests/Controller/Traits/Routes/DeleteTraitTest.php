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
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\DeleteTrait
 */
class DeleteTraitTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;

    private $deleteTrait;

    private $form;

    private $user;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->resourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');

        $this->deleteTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\DeleteTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->deleteTrait->expects($this->once())
            ->method('getResourceManager')
            ->with()
            ->willReturn($this->resourceManager);

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = User::create('abc', 5);

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->with('form', $this->user, array('data_class'=>get_class($this->user)))
            ->willReturn($formBuilder = $this->getMock('Symfony\Component\Form\FormBuilderInterface'));
        $formBuilder->expects($this->once())
            ->method('add')
            ->with('submit', 'submit')
            ->willReturnSelf();
        $formBuilder->expects($this->once())
            ->method('setMethod')
            ->with('DELETE')
            ->willReturnSelf();
        $formBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->deleteTrait->expects($this->once())
            ->method('getFormFactory')
            ->willReturn($formFactory);

        $this->deleteTrait->expects($this->once())
            ->method('handleView')
            ->with($this->anything())
            ->willReturnArgument(0);

        $this->resourceManager->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($this->user);
    }

    public function testDelete()
    {
        $view = $this->deleteTrait->removeAction(5);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($this->form, $view->getData());
    }

    public function testDeleteFail()
    {
        $request = new Request();
        $request->setMethod('DELETE');

        $this->deleteTrait->expects($this->once())
            ->method('processForm')
            ->with($this->form, $request)
            ->willReturn(false);

        $view = $this->deleteTrait->deleteAction($request, 5);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($this->form, $view->getData());
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $view->getStatusCode());
    }

    public function testDeleteSuccess()
    {
        $request = new Request();
        $request->setMethod('DELETE');

        $this->deleteTrait->expects($this->once())
            ->method('processForm')
            ->with($this->form, $request)
            ->willReturn(true);

        $this->deleteTrait->expects($this->once())
            ->method('redirectTo')
            ->with('cget')
            ->willReturn($redirect = View::createRouteRedirect('get_users'));

        $view = $this->deleteTrait->deleteAction($request, 5);

        $this->assertSame($redirect, $view);
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $view->getStatusCode());
    }
}
