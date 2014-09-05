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
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\FrontendManagerTagsCompilerPass;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\FrontendManagerTagsCompilerPass
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\Helpers
 */
class FrontendManagerTagsCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected function process(ContainerBuilder $container)
    {
        $pass = new FrontendManagerTagsCompilerPass();
        $pass->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'user'));
        $container->register('acme.demo.user.authorization_checker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));
        $container->register('acme.demo.user.form')
        ->addTag('radrest.form', array('resource'=>'user'));

        $this->process($container);

        $this->assertTrue($container->has('radrest.frontend_manager.compiled.user'));
        $this->assertTrue($container->has('acme.demo.user.frontend_manager'));

        $def = $container->getDefinition('radrest.frontend_manager.compiled.user');
        $this->assertTrue($def->hasTag('radrest.frontend_manager'));
        $tag = $def->getTag('radrest.frontend_manager');
        $this->assertTrue(isset($tag[0]['resource']));
        $this->assertEquals($tag[0]['resource'], 'user');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(0));
        $this->assertEquals('acme.demo.user.resource_manager', (string)$def->getArgument(0));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(1));
        $this->assertEquals('acme.demo.user.authorization_checker', (string)$def->getArgument(1));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(2));
        $this->assertEquals('acme.demo.user.form', (string)$def->getArgument(2));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(3));
        $this->assertEquals('form.factory', (string)$def->getArgument(3));
    }

    public function testProcessMultipleResources()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'user'));
        $container->register('acme.demo.user.authorization_checker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));
        $container->register('acme.demo.user.form')
        ->addTag('radrest.form', array('resource'=>'user'));

        $container->register('acme.demo.note.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'note'));
        $container->register('acme.demo.note.authorization_checker')
        ->addTag('radrest.authorization_checker', array('resource'=>'note'));
        $container->register('acme.demo.note.form')
        ->addTag('radrest.form', array('resource'=>'note'));

        $this->process($container);

        $this->assertTrue($container->has('radrest.frontend_manager.compiled.user'));
        $this->assertTrue($container->has('acme.demo.user.frontend_manager'));

        $def = $container->getDefinition('radrest.frontend_manager.compiled.user');
        $this->assertTrue($def->hasTag('radrest.frontend_manager'));
        $tag = $def->getTag('radrest.frontend_manager');
        $this->assertTrue(isset($tag[0]['resource']));
        $this->assertEquals($tag[0]['resource'], 'user');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(0));
        $this->assertEquals('acme.demo.user.resource_manager', (string)$def->getArgument(0));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(1));
        $this->assertEquals('acme.demo.user.authorization_checker', (string)$def->getArgument(1));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(2));
        $this->assertEquals('acme.demo.user.form', (string)$def->getArgument(2));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(3));
        $this->assertEquals('form.factory', (string)$def->getArgument(3));

        $this->assertTrue($container->has('radrest.frontend_manager.compiled.note'));
        $this->assertTrue($container->has('acme.demo.note.frontend_manager'));

        $def = $container->getDefinition('radrest.frontend_manager.compiled.note');
        $this->assertTrue($def->hasTag('radrest.frontend_manager'));
        $tag = $def->getTag('radrest.frontend_manager');
        $this->assertTrue(isset($tag[0]['resource']));
        $this->assertEquals($tag[0]['resource'], 'note');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(0));
        $this->assertEquals('acme.demo.note.resource_manager', (string)$def->getArgument(0));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(1));
        $this->assertEquals('acme.demo.note.authorization_checker', (string)$def->getArgument(1));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(2));
        $this->assertEquals('acme.demo.note.form', (string)$def->getArgument(2));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(3));
        $this->assertEquals('form.factory', (string)$def->getArgument(3));

    }

    public function testProcessNoForm()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'user'));
        $container->register('acme.demo.user.authorization_checker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));

        $this->process($container);

        $this->assertTrue($container->has('radrest.frontend_manager.compiled.user'));
        $this->assertTrue($container->has('acme.demo.user.frontend_manager'));

        $def = $container->getDefinition('radrest.frontend_manager.compiled.user');
        $this->assertTrue($def->hasTag('radrest.frontend_manager'));
        $tag = $def->getTag('radrest.frontend_manager');
        $this->assertTrue(isset($tag[0]['resource']));
        $this->assertEquals($tag[0]['resource'], 'user');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(0));
        $this->assertEquals('acme.demo.user.resource_manager', (string)$def->getArgument(0));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(1));
        $this->assertEquals('acme.demo.user.authorization_checker', (string)$def->getArgument(1));
        $this->assertNull($def->getArgument(2));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(3));
        $this->assertEquals('form.factory', (string)$def->getArgument(3));
    }

    /**
     * @expectedException LogicException
     */
    public function testProcessNoAuthorizationChecker()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'user'));
        $container->register('acme.demo.user.form')
        ->addTag('radrest.form', array('resource'=>'user'));

        $this->process($container);
    }

    public function testProcessNoAliasPossible()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'user'));
        $container->register('acme.blah.user.authorization_checker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));
        $container->register('acme.demo.user.form')
        ->addTag('radrest.form', array('resource'=>'user'));

        $this->process($container);

        $this->assertTrue($container->has('radrest.frontend_manager.compiled.user'));
        $this->assertFalse($container->has('acme.demo.user.frontend_manager'));
    }

    public function testProcessAliasTaken()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.resource_manager')
        ->addTag('radrest.resource_manager', array('resource'=>'user'));
        $container->register('acme.demo.user.authorization_checker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));
        $container->register('acme.demo.user.form')
        ->addTag('radrest.form', array('resource'=>'user'));

        $container->register('acme.demo.user.frontend_manager');

        $this->process($container);

        $this->assertTrue($container->has('radrest.frontend_manager.compiled.user'));
        $this->assertFalse($container->hasAlias('acme.demo.user.frontend_manager'));

        $def = $container->getDefinition('radrest.frontend_manager.compiled.user');
        $this->assertTrue($def->hasTag('radrest.frontend_manager'));
        $tag = $def->getTag('radrest.frontend_manager');
        $this->assertTrue(isset($tag[0]['resource']));
        $this->assertEquals($tag[0]['resource'], 'user');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(0));
        $this->assertEquals('acme.demo.user.resource_manager', (string)$def->getArgument(0));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(1));
        $this->assertEquals('acme.demo.user.authorization_checker', (string)$def->getArgument(1));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(2));
        $this->assertEquals('acme.demo.user.form', (string)$def->getArgument(2));
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $def->getArgument(3));
        $this->assertEquals('form.factory', (string)$def->getArgument(3));
    }
}
