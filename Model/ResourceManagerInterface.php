<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Model;

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
	 * @return array
	 */
	public function findAll();

	/**
	 * Finds the object by its database ID
	 *
	 * @param int $id
	 * @return object
	*/
	public function find($id);

	/**
	 * Creates a new object, does not persist it to the database yet.
	 *
	 * @return object
	*/
	public function create();

	/**
	 * Updates or creates an object in the database
	 *
	 * @param object $object
	 * @throws \Exception if the object cannot be updated/created
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
