<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes;

use vierbergenlars\Bundle\RadRestBundle\View\View;
use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;

/**
 * Base trait for all route related actions
 */
trait AbstractBaseTrait
{
    /**
     * Get the resource manager for the controller
     * @return ResourceManagerInterface
     */
    abstract public function getResourceManager();

    /**
     * Manipulate the output of the controller
     * @param View $view
     * @return View
     */
    abstract protected function handleView(View $view);
}
