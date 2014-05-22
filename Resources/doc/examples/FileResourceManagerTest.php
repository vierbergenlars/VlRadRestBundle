<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/FileResourceManager.php';

/**
 * @codeCoverageIgnore
 */
class FileResourceManagerTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;
    public function setUp()
    {
        @mkdir(__DIR__.'/scratch');
        file_put_contents(__DIR__.'/scratch/22.txt', '22');
        file_put_contents(__DIR__.'/scratch/89.jpg', '896');
        $this->resourceManager = new FileResourceManager(__DIR__.'/scratch');
        clearstatcache();
    }
    
    public function tearDown()
    {
        foreach(scandir(__DIR__.'/scratch') as $file)
        {
            if(is_file(__DIR__.'/scratch/'.$file))
                unlink(__DIR__.'/scratch/'.$file);
        }
        rmdir(__DIR__.'/scratch');
    }
    
    public function testFindAll()
    {
        $ret = $this->resourceManager->findAll();
        $this->assertContainsOnlyInstancesOf('File', $ret);
        $this->assertCount(2, $ret);
        $this->assertContains($this->resourceManager->find('22.txt'), $ret, '', false, false);
        $this->assertContains($this->resourceManager->find('89.jpg'), $ret, '', false, false);
    }
    
    public function testFind()
    {
        $ret = $this->resourceManager->find('22.txt');
        $this->assertInstanceOf('File', $ret);
        $this->assertEquals('22.txt', $ret->getFilename());
        $this->assertEquals('22', $ret->getData());
    }
    
    public function testFindFail()
    {
        $this->assertNull($this->resourceManager->find('98461'));
    }
    
    public function testUpdate()
    {
        $f = $this->resourceManager->find('22.txt');
        $f->setData('12');
        $this->resourceManager->update($f);
        $this->assertEquals('12', file_get_contents(__DIR__.'/scratch/22.txt'));
    }
    
    /**
     * @expectedException LogicException
     */
    public function testUpdateWrongType()
    {
        $this->resourceManager->update(new stdClass());
    }
    
    public function testCreate()
    {
        $f = $this->resourceManager->create();
        $f->setFilename('2989.txt');
        $f->setData('3269');
        $this->resourceManager->update($f);
        $this->assertEquals('3269', file_get_contents(__DIR__.'/scratch/2989.txt'));
    }
    
    public function testDelete()
    {
        $f = $this->resourceManager->find('22.txt');
        $this->resourceManager->delete($f);
        $this->assertFalse(is_file(__DIR__.'/scratch/22.txt'));
    }
    
    /**
     * @expectedException LogicException
     */
    public function testDeleteWrongType()
    {
        $this->resourceManager->delete(new stdClass());
    }
}