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

use vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController;

class UserServiceController extends ControllerServiceController
{
    public function _redirectTo($nextAction, array $params = array())
    {
        return $this->redirectTo($nextAction, $params);
    }

    public function _getLogger()
    {
        return $this->getLogger();
    }

    public function _getRouter()
    {
        return $this->getRouter();
    }

    public function _getFormFactory()
    {
        return $this->getFormFactory();
    }

    public function _getServiceName()
    {
        return $this->getServiceName();
    }

    public function getSerializationGroups($action)
    {
        if($action === 'cget') {
            return array('abc', 'def');
        }
        return parent::getSerializationGroups($action);
    }
}
