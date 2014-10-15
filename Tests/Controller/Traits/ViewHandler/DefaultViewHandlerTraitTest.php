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

use vierbergenlars\Bundle\RadRestBundle\View\View;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\ViewHandlerController;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\ViewHandler\DefaultViewHandlerTrait
 */
class DefaultViewHandlerTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testhandleView()
    {
        $viewHandler = new ViewHandlerController();

        $view = new View();
        $this->assertEquals($view, $viewHandler->handleView($view));
        $this->assertArrayHasKey('controller', $view->getExtraData());
    }
}
