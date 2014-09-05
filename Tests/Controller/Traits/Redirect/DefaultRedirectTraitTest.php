<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Redirect;

use vierbergenlars\Bundle\RadRestBundle\View\View;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Redirect\DefaultRedirectTrait
 */
class DefaultRedirectTraitTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        if(!trait_exists(__NAMESPACE__.'\\_DefaultRedirectTrait', false)) {
            // Expose DefaultRedirectTrait::redirectTo as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Redirect\DefaultRedirectTrait;
                trait _DefaultRedirectTrait {
                    use DefaultRedirectTrait { redirectTo as public;}
                }'
            );
        }
    }

    public function testRedirectToWithParameters()
    {
        $redirectTrait = $this->getMockBuilder(__NAMESPACE__.'\_DefaultRedirectTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $redirectTrait->expects($this->once())
            ->method('getRouteName')
            ->with('get')
            ->willReturn('get_user');


        $view = $redirectTrait->redirectTo('get', array('id'=>3));
        $this->assertTrue($view instanceof View);
        $this->assertEquals('get_user', $view->getRoute());
        $this->assertEquals(array('id'=>3), $view->getRouteParameters());
    }

    public function testRedirectToWithoutParameters()
    {
        $redirectTrait = $this->getMockBuilder(__NAMESPACE__.'\_DefaultRedirectTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $redirectTrait->expects($this->once())
            ->method('getRouteName')
            ->with('get')
            ->willReturn('get_user');


        $view = $redirectTrait->redirectTo('get');
        $this->assertTrue($view instanceof View);
        $this->assertEquals('get_user', $view->getRoute());
        $this->assertEquals(array(), $view->getRouteParameters());
    }
}
