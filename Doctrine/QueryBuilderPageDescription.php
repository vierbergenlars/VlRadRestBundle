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

use Doctrine\ORM\QueryBuilder;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

/**
 * Page description implementation for Doctrine QueryBuilders
 * Used by EntityRepository for pagination
 * @internal
 */
class QueryBuilderPageDescription implements PageDescriptionInterface
{
    protected $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getTotalItemCount()
    {
        $aliases = $this->queryBuilder->getDQLPart('select');
        $qb = clone $this->queryBuilder;
        return $qb
        ->select(sprintf('COUNT(%s)', $aliases[0]))
        ->getQuery()
        ->getSingleScalarResult()
        ;
    }

    public function getSlice($offset, $limit)
    {
        $qb = clone $this->queryBuilder;
        return $qb
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult()
        ;
    }
}
