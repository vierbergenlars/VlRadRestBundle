<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Doctrine;

use Doctrine\ORM\EntityRepository as DoctrineRepository;
use vierbergenlars\Bundle\RadRestBundle\Model\ResourceManagerInterface;

class EntityRepository extends DoctrineRepository implements ResourceManagerInterface
{
	private function _validateObject($object, $calledMethod)
	{
		$expected_class = $this->getEntityName();
		if(!is_a($object, $expected_class)) {
			throw new \LogicException(sprintf(
					'%s::%s() requires its first argument to be an instance of %s, got an instance of %s.',
					get_class(),
					$calledMethod,
					$expected_class,
					get_class($object)
			));
		}
	}

	public function create()
	{
		return $this->getClassMetadata()->newInstance();
	}

	public function update($object)
	{
		$this->_validateObject($object, 'update');
		$this->getEntityManager()->persist($object);
		$this->getEntityManager()->flush($object);
	}

	public function delete($object)
	{
		$this->_validateObject($object, 'delete');
		$this->getEntityManager()->remove($object);
		$this->getEntityManager()->flush($object);
	}

	public function findByCanonicalName($cname)
	{
		return $this->find($cname);
	}

	public function getCanonicalName($object)
	{
		$this->_validateObject($object, 'getCanonicalName');
		return $object->getId();
	}
}
