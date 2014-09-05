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

use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

/**
 * This trait provides a default implementation for pagination
 */
trait DefaultPaginationTrait
{
    /**
     * Gets a slice of the page description for one page
     * @param PageDescriptionInterface $pageDescription
     * @param int $page
     * @return array<object>
     */
    protected function getPagination(PageDescriptionInterface $pageDescription, $page)
    {
        return $pageDescription->getSlice($page<1?0:($page-1)*10, 10);
    }
}
