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
use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;

class EntityRepository extends DoctrineRepository implements ResourceManagerInterface
{
    /**
     * @param string $calledMethod Method that was called on this object, to create a nice exception message.
     */
    private function validateObject($object, $calledMethod)
    {
        $expectedClass = $this->getEntityName();
        if(is_a($object, $expectedClass)) {
            return;
        }
        if(is_object($object)) {
            throw new \LogicException(sprintf(
                '%s::%s() requires its first argument to be an instance of %s, got an instance of %s.',
                get_class(),
                $calledMethod,
                $expectedClass,
                get_class($object)
            ));
        } else {
            throw new \LogicException(sprintf(
                '%s::%s() requires its first argument to be an instance of %s, got a %s.',
                get_class(),
                $calledMethod,
                $expectedClass,
                gettype($object)
            ));
        }
    }

    public function create()
    {
        return $this->getClassMetadata()->newInstance();
    }

    public function update($object)
    {
        $this->validateObject($object, 'update');
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush($object);
    }

    public function delete($object)
    {
        $this->validateObject($object, 'delete');
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush($object);
    }
}
