<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OverriddenMethodController extends RadRestController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        if($container->has('frontend_manager')) {
            $this->setFrontendManager($container->get('frontend_manager'));
        }
    }

    public function getAction($id)
    {
        return null;
    }
}
