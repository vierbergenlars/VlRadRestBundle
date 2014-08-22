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

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\AuthorizationCheckerCompilerPass
 */
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
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security\UserAuthorizationChecker')
        ->addTag('radrest.authorization_checker', array('resource'=>'user'));

        $this->process($container);

        $def = $container->getDefinition('acme.demo.user.authorization_checker');
        $this->assertCount(3, $def->getArguments());
        $this->assertSame(AuthorizationCheckerCompilerPass::ROLE_HIERARCHY_SERVICE, (string)$def->getArgument(0));
        $this->assertSame(AuthorizationCheckerCompilerPass::SECURITY_CONTEXT_SERVICE, (string)$def->getArgument(1));
        $this->assertSame(AuthorizationCheckerCompilerPass::TRUST_RESOLVER_SERVICE, (string)$def->getArgument(2));
    }
}
