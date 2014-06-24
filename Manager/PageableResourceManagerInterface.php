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

use Knp\Component\Pager\Pagination\PaginationInterface;

interface PageableResourceManagerInterface extends ResourceManagerInterface
{
    /**
     * Gets a page of the collection of objects
     * @param int $page
     * @param int $itemsPerPage
     * @return PaginationInterface
     */
    public function getPage($page, $itemsPerPage);
}
