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

class NoteServiceController extends ControllerServiceController
{
    public function getRouteName($action)
    {
        switch($action) {
            case 'cget':
                return 'get_notes';
            case 'get':
                return 'get_note';
            default:
                return parent::getRouteName($action);
        }
    }
}
