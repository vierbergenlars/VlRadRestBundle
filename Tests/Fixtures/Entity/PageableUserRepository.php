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

use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageableInterface;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Pagination\ArrayPageDescription;

class PageableUserRepository implements ResourceManagerInterface, PageableInterface
{
    public $fakeUsers;
    public function findAll()
    {
        return $this->fakeUsers;
    }

    public function getPageDescription()
    {
        return new ArrayPageDescription($this->fakeUsers);
    }

    public function find($id)
    {
        return $this->fakeUser;
    }

    public function create()
    {
        return $this->fakeUser;
    }

    public function update($object)
    {

    }

    public function delete($object)
    {

    }
}
