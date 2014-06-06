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

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler\FrontendManagerTagsCompilerPass;

/**
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class VlRadRestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FrontendManagerTagsCompilerPass());
    }
}
