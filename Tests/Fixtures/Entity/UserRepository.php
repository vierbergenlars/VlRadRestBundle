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

class UserRepository implements ResourceManagerInterface
{
    public $fakeUser;
    public function findAll()
    {
        return array($this->fakeUser);
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
