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
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;

class UserController extends RadRestController
{
    public function _redirectTo($nextAction, array $params = array())
    {
        return $this->redirectTo($nextAction, $params);
    }

    public function getResourceManager()
    {
        return $this->container->get('resource_manager');
    }

    public function getFormType()
    {
        return $this->container->get('form');
    }

    public function getSerializationGroups($action)
    {
        if($action === 'cget') {
            return array('abc', 'def');
        }
        return parent::getSerializationGroups($action);
    }
}
