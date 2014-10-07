<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Manager;

use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

/**
 * Manager interface for all objects controlled by the REST controller
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
interface ResourceManagerInterface
{

    /**
     * Gets the whole collection of objects
     *
     * @return PageDescriptionInterface
     */
    public function getPageDescription();

    /**
     * Finds the object by its database ID
     *
     * @param string|int $id
     * @return object|null
    */
    public function find($id);

    /**
     * Creates a blank instance of the managed object
     *
     * @return object
     */
    public function newInstance();

    /**
     * Creates an object in the database
     *
     * @param object $object
     * @throws \Exception if the object cannot be created
     * @return void
    */
    public function create($object);

    /**
     * Updates an object in the database
     *
     * @param object $object
     * @throws \Exception if the object cannot be updated
     * @return void
    */
    public function update($object);

    /**
     * Deletes an object from the database
     *
     * @param object $object
     * @throws \Exception if the object cannot be deleted
     * @return void
    */
    public function delete($object);
}
