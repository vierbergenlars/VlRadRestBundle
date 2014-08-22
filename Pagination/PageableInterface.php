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
 * Marks a resource manager that can be paginated.
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
interface PageableInterface
{
    /**
     * @return PageDescriptionInterface
     */
    public function getPageDescription();
}
