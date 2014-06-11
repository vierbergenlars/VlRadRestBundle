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

class SwitchedSerializationServiceController extends ControllerServiceController
{
    public function getSerializationGroups()
    {
        return array('object'=>array('abc', 'def'));
    }
}
