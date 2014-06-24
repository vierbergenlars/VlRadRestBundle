<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity;

use vierbergenlars\Bundle\RadRestBundle\Manager\PageableResourceManagerInterface;
use Knp\Component\Pager\Paginator;

class PaginateableUserRepository extends UserRepository implements PageableResourceManagerInterface
{
    public $items = array();
    public function getPage($page, $itemsPerPage)
    {
        $paginator = new Paginator();
        return $paginator->paginate($this->items, $page, $itemsPerPage);
    }

    static public function createWithRandomItems($numItems = 0)
    {
        $resourceManager = new self();
        $resourceManager->items = array_map(function($chr) {
            return User::create(md5($chr), $chr);
        }, range(0, $numItems));

        return $resourceManager;
    }
}
