<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Security;

/**
 * Authorization checker interface for all objects controlled by the REST controller
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
interface AuthorizationCheckerInterface
{

    /**
     * Checks authorization to list all objects
     *
     * @return bool
     */
    public function mayList();

    /**
     * Checks authorization to create this new object
     *
     * @param object $object
     * @return bool
    */
    public function mayCreate($object);

    /**
     * Checks authorization to view a specific object
     *
     * @param object $object
     * @return bool
    */
    public function mayView($object);

    /**
     * Checks authorization to edit a specific object
     * @param object $object
     * @return bool
    */
    public function mayEdit($object);

    /**
     * Checks authorization to delete a specific object
     * @param object $object
     * @return bool
    */
    public function mayDelete($object);
}
