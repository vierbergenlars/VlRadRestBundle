<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Routing;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultClassRoutingTrait
 */
class DefaultClassRoutingTraitTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        if(!class_exists(__NAMESPACE__.'\\_DefaultClassRoutingTraitClass', false)) {
            // Expose DefaultClassRoutingTrait::getActionResourceName as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultClassRoutingTrait;
                class _DefaultClassRoutingTraitClass {
                    use DefaultClassRoutingTrait { getActionResourceName as public;}
                    protected function getRouter() {}
                    protected function getLogger() {}
                }'
            );
        }
    }

    public function testGetActionResourceName()
    {
        $classRouting = new _DefaultClassRoutingTraitClass;

        $this->assertEquals(__NAMESPACE__.'\\_DefaultClassRoutingTraitClass::getAction', $classRouting->getActionResourceName('get'));
        $this->assertEquals(__NAMESPACE__.'\\_DefaultClassRoutingTraitClass::putAction', $classRouting->getActionResourceName('put'));
        $this->assertEquals(__NAMESPACE__.'\\_DefaultClassRoutingTraitClass::adbDeAction', $classRouting->getActionResourceName('adbDe'));
    }
}
