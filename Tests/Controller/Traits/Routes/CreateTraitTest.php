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
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\CreateTrait
 */
class CreateTraitTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;

    private $createTrait;

    private $form;

    private $user;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->resourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');

        $this->createTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\CreateTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->createTrait->expects($this->once())
            ->method('getResourceManager')
            ->with()
            ->willReturn($this->resourceManager);

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->createTrait->expects($this->once())
            ->method('createForm')
            ->with($this->anything(), 'POST')
            ->willReturn($this->form);

        $this->createTrait->expects($this->once())
            ->method('handleView')
            ->with($this->anything())
            ->willReturnArgument(0);

        $this->resourceManager->expects($this->once())
            ->method('newInstance')
            ->with()
            ->willReturn($this->user = new User());

    }

    public function testNew()
    {
        $view = $this->createTrait->newAction();

        $this->assertTrue($view instanceof View);
        $this->assertEquals($this->form, $view->getData());
    }

    public function testPostFail()
    {
        $request = new Request();
        $request->setMethod('POST');

        $this->createTrait->expects($this->once())
            ->method('processForm')
            ->with($this->form, $request)
            ->willReturn(false);

        $view = $this->createTrait->postAction($request);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($this->form, $view->getData());
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $view->getStatusCode());
    }

    public function testPostSuccess()
    {
        $request = new Request();
        $request->setMethod('POST');

        $this->createTrait->expects($this->once())
            ->method('processForm')
            ->with($this->form, $request)
            ->willReturn(true);

        $this->form->expects($this->atLeastOnce())
            ->method('getData')
            ->with()
            ->willReturn(User::create('abc', 5));

        $this->createTrait->expects($this->once())
            ->method('redirectTo')
            ->with('get', array('id'=>5))
            ->willReturn($redirect = View::createRouteRedirect('get_user', array('id'=>5)));

        $view = $this->createTrait->postAction($request);

        $this->assertSame($redirect, $view);
        $this->assertEquals(Codes::HTTP_CREATED, $view->getStatusCode());
    }
}
