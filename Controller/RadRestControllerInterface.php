<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;

interface RadRestControllerInterface
{
    /**
     * Returns a list of serializer groups for each type of GET request (list & single object view)
     * @return array<string, string[]>
     */
    public function getSerializationGroups();

    /**
     * Returns the frontend manager used for this controller
     * @return FrontendManager;
     */
    public function getFrontendManager();
}
