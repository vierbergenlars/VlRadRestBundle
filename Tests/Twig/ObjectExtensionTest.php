<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Twig;

use vierbergenlars\Bundle\RadRestBundle\Twig\ObjectExtension;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Twig\ObjectExtension
 */
class ObjectExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ObjectExtension
     */
    private $extension;

    protected function setUp()
    {
        if(!class_exists('Twig_Extension')) {
            $this->markTestSkipped('Twig is required for this test');
        }
        $this->extension = new ObjectExtension();
    }

    public function testObjectKeys()
    {
        $user = User::create('abc');
        $keys = $this->extension->objectKeys($user);
        $this->assertEquals(array('id', 'username', 'email', 'nose'), $keys);
    }

    /**
     * @dataProvider objectStringifyProvider
     */
    public function testObjectStringify($value, $result)
    {
        $this->assertEquals($result, $this->extension->objectStringify($value));
    }

    public static function objectStringifyProvider()
    {
        return array(
            array(false, 'N'),
            array(true, 'Y'),
            array(125, '125'),
            array(1.0258, '1.0258'),
            array('abceef', 'abceef'),
            array(array('a'=>'b'), '(Array)'),
            array(fopen(__FILE__, 'r'), '(Resource)'),
            array(null, '(NULL)'),
            array(new \DateTime('@8888'), '1970-01-01T02:28:08+0000'),
            array(User::create('abc'), 'abc'),
            array(new \stdClass(), '(Object)'),
        );
    }

    public function testTwigIntegration()
    {
        $twig = new \Twig_Environment();
        $twig->setLoader(new \Twig_Loader_String());
        $twig->addExtension($this->extension);

        $template = $twig->loadTemplate(<<<TWIG
            <table>
                <tr>
                    {% for key in obj|radrest_object_keys %}
                        <th>{{ key }}</th>
                    {% endfor %}
                </tr>
                <tr>
                    {% for key in obj|radrest_object_keys %}
                        <td>{{ attribute(obj, key)|radrest_object_stringify }}</td>
                    {% endfor %}
                </tr>
            </table>
TWIG
        );

        $obj = User::create('abcde', 5);
        $obj->nose = new \DateTime('@8888');
        $result = $template->render(array('obj'=>$obj));

        $this->assertEquals(str_replace(array(' ', "\r", "\n"), '',<<<RES
            <table>
                <tr>
                    <th>id</th>
                    <th>username</th>
                    <th>email</th>
                    <th>nose</th>
                </tr>
                <tr>
                    <td>5</td>
                    <td>abcde</td>
                    <td>abcde@example.com</td>
                    <td>1970-01-01T02:28:08+0000</td>
                </tr>
            </table>
RES
        ), str_replace(array(' ', "\r", "\n"), '', $result));
    }
}
