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
use FOS\RestBundle\View\View;

class NoteServiceController extends ControllerServiceController
{
    protected function redirectTo($nextAction, array $params = array())
    {
        switch($nextAction) {
            case 'cget':
                return View::createRouteRedirect('get_notes', $params);
            case 'get':
                return View::createRouteRedirect('get_note', $params);
        }
    }
}
