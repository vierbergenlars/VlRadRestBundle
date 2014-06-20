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
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\EntityRepositoryCompilerPass;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\EntityRepositoryCompilerPass
 */
class EntityRepositoryCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected function process(ContainerBuilder $container)
    {
        $pass = new EntityRepositoryCompilerPass();
        $pass->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;

        $container->register('acme.demo.user.entity_repository')
        ->setClass('Acme\DemoBundle\Entity\User')
        ->addTag('radrest.entity_repository');

        $container->register('acme.demo.note.entity_repository')
        ->setClass('Acme\DemoBundle\Entity\Note')
        ->addTag('radrest.entity_repository', array('entity_manager'=>'doctrine.orm.default_entity_manager'));

        $this->process($container);

        $def = $container->getDefinition('acme.demo.user.entity_repository');
        $this->assertSame('doctrine.orm.entity_manager', $def->getFactoryService());
        $this->assertSame('getRepository', $def->getFactoryMethod());
        $this->assertSame(array('Acme\DemoBundle\Entity\User'), $def->getArguments());

        $def = $container->getDefinition('acme.demo.note.entity_repository');
        $this->assertSame('doctrine.orm.default_entity_manager', $def->getFactoryService());
        $this->assertSame('getRepository', $def->getFactoryMethod());
        $this->assertSame(array('Acme\DemoBundle\Entity\Note'), $def->getArguments());
    }
}
