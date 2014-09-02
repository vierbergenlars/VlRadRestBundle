<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization;

/**
 * This trait provides the default implementation for serialization groups
 */
trait DefaultSerializationGroupsTrait
{
    /**
     * Returns a list of serializer groups for the given action on this controller
     *
     * @param string $action
     * @return string[] Serialization groups for this action
     */
    public function getSerializationGroups($action)
    {
        return array('Default');
    }
}
