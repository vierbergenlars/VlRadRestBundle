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

use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;
use Symfony\Component\Form\FormTypeInterface;

interface RadRestControllerInterface
{
    /**
     * Returns a list of serializer groups for the given action on this controller
     *
     * @param string $action
     * @return array<string> Serialization groups for this action
     */
    public function getSerializationGroups($action);

    /**
     * Returns the resource manager of this controller
     * @return ResourceManagerInterface
     */
    public function getResourceManager();

    /**
     * Returns the form type of this controller
     * @return FormTypeInterface
     */
    public function getFormType();

    /**
     * Returns the route name for an action on this controller
     * @param string $action enum['cget', 'get', 'new', 'post', 'edit', 'put', 'remove', 'delete']
     * @return string
     */
    public function getRouteName($action);
}
