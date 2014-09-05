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
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\PatchTrait
 */
class PatchTraitTest extends \PHPUnit_Framework_TestCase
{
    private $frontendManager;

    private $patchTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->frontendManager = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->patchTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\PatchTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->patchTrait->expects($this->once())
            ->method('getFrontendManager')
            ->with()
            ->willReturn($this->frontendManager);

        $this->patchTrait->expects($this->once())
            ->method('handleView')
            ->with($this->anything())
            ->willReturnArgument(0);
    }

    public function testPatchFail()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request();
        $request->setMethod('PATCH');

        $this->frontendManager->expects($this->once())
            ->method('editResource')
            ->with(5, $request, true)
            ->willReturn($form);

        $view = $this->patchTrait->patchAction($request, 5);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($form, $view->getData());
        $this->assertEquals('form', $view->getTemplateVar());
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $view->getStatusCode());
    }

    public function testPatchSuccess()
    {
        $request = new Request();
        $request->setMethod('PATCH');

        $this->frontendManager->expects($this->once())
            ->method('editResource')
            ->with(5, $request, true)
            ->willReturn($user = User::create('abcde', 5));

        $this->patchTrait->expects($this->once())
            ->method('redirectTo')
            ->with('get', array('id'=>5))
            ->willReturn($redirect = View::createRouteRedirect('get_user', array('id'=>5)));

        $view = $this->patchTrait->patchAction($request, 5);

        $this->assertSame($redirect, $view);
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $view->getStatusCode());
    }
}
