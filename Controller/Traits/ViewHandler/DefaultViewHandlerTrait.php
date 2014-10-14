<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\ViewHandler;

use vierbergenlars\Bundle\RadRestBundle\View\View;
use vierbergenlars\Bundle\RadRestBundle\Twig\ControllerVariables;

/**
 * This trait provides the default implementation for handling views.
 * By default injects controller variables in the view
 */
trait DefaultViewHandlerTrait
{
    /**
     * Handles the view before it is returned
     * @param View $view
     */
    protected function handleView(View $view)
    {
        $view->setExtraData(array(
            'controller' => new ControllerVariables($this),
        ));
        return $view;
    }
}
