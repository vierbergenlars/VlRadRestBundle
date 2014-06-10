<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\DepencencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\Helpers;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\Helpers
 */
class HelpersTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterAliasIfNotExists()
    {
        $container = new ContainerBuilder();
        $helper = new Helpers($container);

        $container->register('a');
        $container->register('b');

        $this->assertTrue($helper->registerAliasIfNotExists('c', 'b'));
        $this->assertFalse($helper->registerAliasIfNotExists('b', 'a'));
        $this->assertFalse($helper->registerAliasIfNotExists('c', 'a'));
    }

    public function testFindTaggedServiceIdByAttributes()
    {
        $container = new ContainerBuilder();
        $helper = new Helpers($container);

        $container->register('a')->addTag('tag');
        $container->register('b')->addTag('tag', array('x'=>'y'));
        $container->register('c')->addTag('tag', array('x'=>'y', 'z'=>2));
        $container->register('d')->addTag('tag', array('x'=>'x'));
        $container->register('e')->addTag('tag', array('x'=>'x', 'z'=>2));

        $this->assertEquals('c', $helper->findTaggedServiceIdByAttributes('tag', array('x'=>'y', 'z'=>2)));
        $this->assertNull($helper->findTaggedServiceIdByAttributes('tag', array('x'=>'a', 'z'=>2)));
        $this->assertNull($helper->findTaggedServiceIdByAttributes('tag', array('a'=>'b')));
        $this->assertEquals('d', $helper->findTaggedServiceIdByAttributes('tag', array('x'=>'x')));
        $this->assertEquals('c', $helper->findTaggedServiceIdByAttributes('tag', array('z'=>2)));
        $this->assertNull($helper->findTaggedServiceIdByAttributes('tag', array('f'=>'x')));
        $this->assertNull($helper->findTaggedServiceIdByAttributes('tag2', array('x'=>'y')));
    }
}
