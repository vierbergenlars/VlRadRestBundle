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

class EmptyPageDescription implements PageDescriptionInterface
{
    public function getTotalItemCount()
    {
        return 0;
    }

    public function getSlice($offset, $limit)
    {
        return array();
    }
}
