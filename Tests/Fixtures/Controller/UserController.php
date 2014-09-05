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
    private $frontendManager;

    public function _redirectTo($nextAction, array $params = array())
    {
        return $this->redirectTo($nextAction, $params);
    }

    public function setFrontendManager(FrontendManager $frontendManager)
    {
        $this->frontendManager = $frontendManager;
    }

    public function getFrontendManager()
    {
        if($this->container->has('frontend_manager')) {
            return $this->get('frontend_manager');
        } else {
            return $this->frontendManager;
        }
    }

    public function getSerializationGroups($action)
    {
        if($action === 'cget') {
            return array('abc', 'def');
        }
    }
}
