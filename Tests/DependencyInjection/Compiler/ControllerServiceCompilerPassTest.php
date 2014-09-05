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
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\ControllerServiceCompilerPass;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\ControllerServiceCompilerPass
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\Helpers
 */
class ControllerServiceCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected function process(ContainerBuilder $container)
    {
        $pass = new ControllerServiceCompilerPass();
        $pass->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;

        // Default constructor
        $container->register('acme.demo.user.frontend_manager')
        ->addTag('radrest.frontend_manager', array('resource'=>'user'));
        $container->register('acme.demo.user.controller')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserServiceController')
        ->addTag('radrest.controller', array('resource'=>'user'));

        // Default constructor, overridden redirectTo method
        $container->register('acme.demo.note.frontend_manager')
        ->addTag('radrest.frontend_manager', array('resource'=>'note'));
        $container->register('acme.demo.note.controller')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\NoteServiceController')
        ->addTag('radrest.controller', array('resource'=>'note'));

        // Overridden constructor, only parameter is frontend manager
        $container->register('acme.demo.file.frontend_manager')
        ->addTag('radrest.frontend_manager', array('resource'=>'file'));
        $container->register('acme.demo.file.controller')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\FileServiceController')
        ->addTag('radrest.controller', array('resource'=>'file'));

        // Overridden constructor, different typehinted and untyped constructor parameters
        $container->register('acme.demo.comment.frontend_manager')
        ->addTag('radrest.frontend_manager', array('resource'=>'comment'));
        $container->register('acme.demo.comment.controller')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\CommentServiceController')
        ->addTag('radrest.controller', array('resource'=>'comment'))
        ->addArgument(new Reference('foo'))
        ->addArgument('abcde');

        // Same
        $container->register('acme.demo.vote.frontend_manager')
        ->addTag('radrest.frontend_manager', array('resource'=>'vote'));
        $container->register('acme.demo.vote.controller')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\VoteServiceController')
        ->addTag('radrest.controller', array('resource'=>'vote'))
        ->addArgument('abcde');

        $this->process($container);

        // Default constructor
        $def = $container->getDefinition('acme.demo.user.controller');
        $this->assertCount(4, $def->getArguments());
        $this->assertEquals('acme.demo.user.frontend_manager', (string)$def->getArgument(0));
        $this->assertEquals('logger', (string)$def->getArgument(1));
        $this->assertEquals('router', (string)$def->getArgument(2));
        $this->assertEquals('acme.demo.user.controller', $def->getArgument(3));

        // Default constructor, overridden redirectTo method
        $def = $container->getDefinition('acme.demo.note.controller');
        $this->assertCount(4, $def->getArguments());
        $this->assertEquals('acme.demo.note.frontend_manager', (string)$def->getArgument(0));
        $this->assertEquals('logger', (string)$def->getArgument(1));
        $this->assertEquals('router', (string)$def->getArgument(2));
        $this->assertEquals('acme.demo.note.controller', $def->getArgument(3));

        // Overridden constructor, only parameter is frontend manager
        $def = $container->getDefinition('acme.demo.file.controller');
        $this->assertCount(1, $def->getArguments());
        $this->assertEquals('acme.demo.file.frontend_manager', (string)$def->getArgument(0));

        // Overridden constructor, different typehinted and untyped constructor parameters
        $def = $container->getDefinition('acme.demo.comment.controller');
        $this->assertCount(4, $def->getArguments());
        $this->assertEquals('foo', (string)$def->getArgument(0));
        $this->assertEquals('logger', (string)$def->getArgument(1));
        $this->assertEquals('acme.demo.comment.frontend_manager', (string)$def->getArgument(2));
        $this->assertEquals('abcde', $def->getArgument(3));

        // Same
        $def = $container->getDefinition('acme.demo.vote.controller');
        $this->assertCount(4, $def->getArguments());
        $this->assertEquals('router', (string)$def->getArgument(0));
        $this->assertEquals('abcde', $def->getArgument(1));
        $this->assertEquals('acme.demo.vote.frontend_manager', (string)$def->getArgument(2));
        $this->assertEquals('logger', (string)$def->getArgument(3));
    }
}
