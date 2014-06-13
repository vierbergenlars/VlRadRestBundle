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
     * Returns a list of serializer groups for the given action on this controller
     *
     * @param string $action
     * @return array<string>|null Serialization groups for this action
     */
    public function getSerializationGroups($action);

    /**
     * Returns the frontend manager used for this controller
     * @return FrontendManager;
     */
    public function getFrontendManager();
}
