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
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Pagination\ArrayPageDescription;

class UserRepository implements ResourceManagerInterface
{
    private $fakeUsers;

    public function setFakeUser($fakeUser)
    {
        $this->fakeUsers = array($fakeUser);
        return $fakeUser;
    }
    public function setFakeUsers($fakeUsers)
    {
        $this->fakeUsers = $fakeUsers;
    }

    public function getPageDescription()
    {
        return new ArrayPageDescription($this->fakeUsers);
    }

    public function find($id)
    {
        return $this->fakeUsers[0];
    }

    public function newInstance()
    {
        return $this->fakeUsers[0];
    }

    public function create($object)
    {

    }

    public function update($object)
    {

    }

    public function delete($object)
    {

    }
}
