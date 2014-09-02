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

class SwitchedSerializationController extends RadRestController
{
    public function getFrontendManager()
    {
        return $this->get('frontend_manager');
    }

    public function getSerializationGroups($action)
    {
        if($action === 'get') {
            return array('abc', 'def');
        }
        return parent::getSerializationGroups($action);
    }
}
