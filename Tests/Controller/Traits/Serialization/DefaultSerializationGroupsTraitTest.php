<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Serialization;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization\DefaultSerializationGroupsTrait
 */
class DefaultSerializationGroupsTraitTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');
    }

    public function testGetSerializationGroups()
    {
        $serializationGroupsTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization\DefaultSerializationGroupsTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->assertEquals(array('Default'), $serializationGroupsTrait->getSerializationGroups('cget'));
        $this->assertEquals(array('Default'), $serializationGroupsTrait->getSerializationGroups('get'));
    }

}
