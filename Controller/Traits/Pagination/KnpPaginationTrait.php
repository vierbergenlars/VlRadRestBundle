<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination;

use Knp\Component\Pager\Paginator;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

/**
 * This trait provides an alternative pagination implementation using the Knp Paginator component
 */
trait KnpPaginationTrait
{
    /**
     * Gets the paginator to use for pagination
     * @return Paginator
     */
    abstract protected function getPaginator();

    /**
     * Gets the configured pagination
     * @param PageDescriptionInterface $pageDescription
     * @param int $page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    protected function getPagination(PageDescriptionInterface $pageDescription, $page)
    {
        return $this->getPaginator()->paginate($pageDescription, $page);
    }
}
