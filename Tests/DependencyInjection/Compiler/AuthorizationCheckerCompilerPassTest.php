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
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\AuthorizationCheckerCompilerPass;

class AuthorizationCheckerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected function process(ContainerBuilder $container)
    {
        $pass = new AuthorizationCheckerCompilerPass();
        $pass->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.authorization_checker')
        ->setClass('Acme\DemoBundle\Security\UserAuthorizationChecker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));

        $container->register('acme.demo.note.authorization_checker')
        ->setClass('Acme\DemoBundle\Security\NoteAuthorizationChecker')
        ->addTag('radrest.authorization_checker', array('resource'=>'note'));

        $container->register('acme.demo.file.authorization_checker')
        ->setClass('Acme\DemoBundle\Security\FileAuthorizationChecker')
        ->addTag('radrest.authorization_checker', array('resource'=>'file', 'factory'=>false));

        $container->register('acme.demo.comment.authorization_checker')
        ->setClass('Acme\DemoBundle\Security\CommentAuthorizationChecker')
        ->setFactoryClass('Acme\DemoBundle\Security\CommentAuthorizationCheckerFactory')
        ->setFactoryMethod('get')
        ->addTag('radrest.authorization_checker', array('resource'=>'comment'));

        $container->register('acme.demo.vote.authorization_checker')
        ->setClass('Acme\DemoBundle\Security\VoteAuthorizationChecker')
        ->setFactoryService('acme.demo.vote.authorization_checker.factory')
        ->setFactoryMethod('get')
        ->addTag('radrest.authorization_checker', array('resource'=>'vote'));

        $this->process($container);

        $def = $container->getDefinition('acme.demo.user.authorization_checker');
        $this->assertSame('radrest.authorization_checker.factory', $def->getFactoryService());
        $this->assertSame('createChecker', $def->getFactoryMethod());
        $this->assertSame(array('Acme\DemoBundle\Security\UserAuthorizationChecker'), $def->getArguments());

        $def = $container->getDefinition('acme.demo.note.authorization_checker');
        $this->assertSame('radrest.authorization_checker.factory', $def->getFactoryService());
        $this->assertSame('createChecker', $def->getFactoryMethod());
        $this->assertSame(array('Acme\DemoBundle\Security\NoteAuthorizationChecker'), $def->getArguments());

        $def = $container->getDefinition('acme.demo.file.authorization_checker');
        $this->assertNull($def->getFactoryService());
        $this->assertNull($def->getFactoryMethod());
        $this->assertSame(array(), $def->getArguments());

        $def = $container->getDefinition('acme.demo.comment.authorization_checker');
        $this->assertNull($def->getFactoryService());
        $this->assertSame('Acme\DemoBundle\Security\CommentAuthorizationCheckerFactory', $def->getFactoryClass());
        $this->assertSame('get', $def->getFactoryMethod());
        $this->assertSame(array(), $def->getArguments());

        $def = $container->getDefinition('acme.demo.vote.authorization_checker');
        $this->assertSame('acme.demo.vote.authorization_checker.factory', $def->getFactoryService());
        $this->assertSame('get', $def->getFactoryMethod());
        $this->assertSame(array(), $def->getArguments());
    }
}
