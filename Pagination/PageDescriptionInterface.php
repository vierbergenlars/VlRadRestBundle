<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Pagination;

/**
 * A wrapper that enables pagination without loading all objects.
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
interface PageDescriptionInterface
{
    /**
     * Get the total number of items that are available
     * @return int
     */
    public function getTotalItemCount();

    /**
     * Get a chunk from the full resultset
     * @param int $offset The starting point in the result set (0 based)
     * @param int $limit The maximum number of items to be in this chunk.
     * @return array
     */
    public function getSlice($offset, $limit);
}
