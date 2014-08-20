<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Pagination;

use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

class ArrayPageDescription implements PageDescriptionInterface
{
    private $a;

    public function __construct(array $a) {
        $this->a = $a;
    }

    public function getTotalItemCount() {
        return count($this->a);
    }

    public function getSlice($offset, $limit) {
        return array_slice($this->a, $offset, $limit);
    }
}