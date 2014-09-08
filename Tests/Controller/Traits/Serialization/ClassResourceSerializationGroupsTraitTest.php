<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Serialization;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization\ClassResourceSerializationGroupsTrait
 */
class ClassResourceSerializationGroupsTraitTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');
    }

    private function getClass($classname)
    {
        $fqcn = __NAMESPACE__.'\\_\\'.$classname;
        if(!class_exists($fqcn, false)) {
            eval(
                'namespace '.__NAMESPACE__.'\\_;
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization\ClassResourceSerializationGroupsTrait;
                class '.$classname.' {
                    use ClassResourceSerializationGroupsTrait;
                }'
            );
        }
        return new $fqcn();
    }

    public function testGetActionResourceName()
    {
        $controller = $this->getClass('UserController');
        $this->assertSame(array('list', 'user_list'), $controller->getSerializationGroups('cget'));
        $this->assertSame(array('object', 'user_object'), $controller->getSerializationGroups('get'));

        $controller = $this->getClass('UserSettingController');
        $this->assertSame(array('list', 'user_setting_list'), $controller->getSerializationGroups('cget'));
        $this->assertSame(array('object', 'user_setting_object'), $controller->getSerializationGroups('get'));
    }
}
