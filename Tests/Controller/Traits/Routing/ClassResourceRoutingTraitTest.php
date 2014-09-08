<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Routing;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\ClassResourceRoutingTrait
 */
class ClassResourceRoutingTraitTest extends \PHPUnit_Framework_TestCase
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
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\ClassResourceRoutingTrait;
                class '.$classname.' {
                    use ClassResourceRoutingTrait;
                }'
            );
        }
        return new $fqcn();
    }

    public function testGetActionResourceName()
    {
        $controller = $this->getClass('UserController');
        $this->assertSame('get_users', $controller->getRouteName('cget'));
        $this->assertSame('get_user', $controller->getRouteName('get'));
        $this->assertSame('put_user', $controller->getRouteName('put'));

        $controller = $this->getClass('UserSettingController');
        $this->assertSame('get_user_settings', $controller->getRouteName('cget'));
        $this->assertSame('get_user_setting', $controller->getRouteName('get'));
        $this->assertSame('put_user_setting', $controller->getRouteName('put'));
    }
}
