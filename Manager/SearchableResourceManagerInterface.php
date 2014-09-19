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

interface SearchableResourceManagerInterface extends ResourceManagerInterface
{
    /**
     * Locates the resources that match certain predicates
     * @param mixed $terms Whatever the resource manager accept as predicate
     * @return \vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface
     */
    public function search($terms);
}
