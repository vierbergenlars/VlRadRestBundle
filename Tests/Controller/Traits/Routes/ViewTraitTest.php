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

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ViewTrait
 */
class ViewTraitTest extends \PHPUnit_Framework_TestCase
{
    private $frontendManager;

    private $viewTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->frontendManager = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ViewTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->viewTrait->expects($this->once())
            ->method('getFrontendManager')
            ->with()
            ->willReturn($this->frontendManager);

        $this->viewTrait->expects($this->once())
            ->method('handleView')
            ->with($this->anything())
            ->willReturnArgument(0);

        $this->viewTrait->expects($this->once())
            ->method('getSerializationGroups')
            ->with('get')
            ->willReturn(array('Default', 'object'));
    }

    public function testView()
    {
        $this->frontendManager->expects($this->once())
            ->method('getResource')
            ->with(5)
            ->willReturn($user = User::create('aaaa', 5));

        $view = $this->viewTrait->getAction(5);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($user, $view->getData());
        $this->assertEquals(array('Default', 'object'), $view->getSerializationContext()->attributes->get('groups')->get());
    }
}
