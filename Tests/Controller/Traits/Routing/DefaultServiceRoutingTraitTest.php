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
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultServiceRoutingTrait
 */
class DefaultServiceRoutingTraitTest extends \PHPUnit_Framework_TestCase
{
    private $routingTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        if(!trait_exists(__NAMESPACE__.'\\_DefaultServiceRoutingTrait', false)) {
            // Expose DefaultServiceRoutingTrait::getActionResourceName as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultServiceRoutingTrait;
                trait _DefaultServiceRoutingTrait {
                    use DefaultServiceRoutingTrait { getActionResourceName as public;}
                }'
            );
        }

        $this->routingTrait = $this->getMockBuilder(__NAMESPACE__.'\_DefaultServiceRoutingTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();
    }

    public function testGetActionResourceName()
    {
        $this->routingTrait->expects($this->exactly(3))
            ->method('getServiceName')
            ->willReturnOnConsecutiveCalls('acme.demo.user.controller', 'user_controller', 'ase_EAe');

        $this->assertEquals('acme.demo.user.controller:getAction', $this->routingTrait->getActionResourceName('get'));
        $this->assertEquals('user_controller:afzFefAction', $this->routingTrait->getActionResourceName('afzFef'));
        $this->assertEquals('ase_EAe:cgetAction', $this->routingTrait->getActionResourceName('cget'));
    }
}
