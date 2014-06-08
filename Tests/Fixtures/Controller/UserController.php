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

class UserController extends RadRestController
{
    /**
     * Override view handler to just return the view
     * @return View
     */
    protected function handleView(View $view)
    {
        return $view;
    }

    public function _redirectTo($nextAction, array $params = array())
    {
        return $this->redirectTo($nextAction, $params);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        if($container->has('frontend_manager')) {
            $this->setFrontendManager($container->get('frontend_manager'));
        }
    }
    
    public function getSerializationGroups()
    {
        return array('list'=>array('abc', 'def'));
    }
}
