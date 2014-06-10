<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\FrontendManagerTagsCompilerPass;
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\AuthorizationCheckerCompilerPass;
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\EntityRepositoryCompilerPass;

/**
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class VlRadRestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FrontendManagerTagsCompilerPass());
        $container->addCompilerPass(new AuthorizationCheckerCompilerPass());
        $container->addCompilerPass(new EntityRepositoryCompilerPass());
    }
}
