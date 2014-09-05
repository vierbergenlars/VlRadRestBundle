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

/**
 * This trait provides the default implementation for handling views.
 * By default a no-op
 */
trait DefaultViewHandlerTrait
{
    /**
     * Handles the view before it is returned
     * @param View $view
     */
    protected function handleView(View $view)
    {
        return $view;
    }
}
