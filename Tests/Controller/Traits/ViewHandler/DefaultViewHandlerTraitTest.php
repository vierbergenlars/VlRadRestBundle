<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\ViewHandler;

use FOS\RestBundle\View\View;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\ViewHandler\DefaultViewHandlerTrait
 */
class DefaultViewHandlerTraitTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        if(!trait_exists(__NAMESPACE__.'\\_DefaultViewHandlerTrait', false)) {
            // Expose DefaultViewHandlerTrait::redirectTo as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\ViewHandler\DefaultViewHandlerTrait;
                trait _DefaultViewHandlerTrait{
                    use DefaultViewHandlerTrait{ handleView as public;}
                }'
            );
        }
    }

    public function testhandleView()
    {
        $viewHandlerTrait = $this->getMockBuilder(__NAMESPACE__.'\_DefaultViewHandlerTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $view = new View();
        $this->assertEquals($view, $viewHandlerTrait->handleView($view));
    }
}
