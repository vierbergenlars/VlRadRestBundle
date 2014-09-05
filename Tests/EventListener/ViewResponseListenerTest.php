<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\EventListener\ViewResponseListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Bridge\Monolog\Logger;
use FOS\RestBundle\Controller\Annotations\View as AView;
use Monolog\Handler\NullHandler;
use vierbergenlars\Bundle\RadRestBundle\View\View;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\EventListener\ViewResponseListener
 * @covers vierbergenlars\Bundle\RadRestBundle\View\View
 */
class ViewResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ViewResponseListener
     */
    private $listener;

    /**
     *
     * @var Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     *
     * @var FOS\RestBundle\View\ViewHandlerInterface
     */
    private $viewHandler;

    protected function setUp()
    {
        $this->viewHandler = $this->getMockBuilder('FOS\RestBundle\View\ViewHandler')
            ->setConstructorArgs(array(array('html'=>true, 'xhtml'=>true, 'json'=>false, 'xml'=>false)))
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $this->templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $this->container = new ContainerBuilder();
        $this->listener = new ViewResponseListener($this->viewHandler, $this->templating, class_exists('Monolog\Logger')?new Logger(__CLASS__, array(new NullHandler())):null);
    }

    private function getFilterEvent(Request $request)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->atLeastOnce())
            ->method('getRequest')
            ->willReturn($request);

        return $event;
    }

    private function getControllerResultEvent(Request $request, $result)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
              ->method('getRequest')
              ->will($this->returnValue($request));
        $event->expects($this->any())
              ->method('getControllerResult')
              ->will($this->returnValue($result));

        return $event;
    }

    /**
     * @dataProvider onKernelControllerProvider
     */
    public function testOnKernelController($action, $exists1, $exists2, $expectedResult)
    {
        if($exists1&&!$exists2) {
            // This would mean that a template exists, and then suddenly disappears. Obviously is never possible IRL
            $this->fail('Should never happen');
        }

        $request = new Request();
        $request->attributes->set('_view', new AView(array('engine'=>'twig')));
        $request->attributes->set('_template', $templateReference = new TemplateReference('AcmeDemoBundle', 'User', $action, 'html', 'twig'));
        $event = $this->getFilterEvent($request);

        $controller = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController');

        $event->expects($this->atLeastOnce())
            ->method('getController')
            ->willReturn(array($controller, $action.'Action'));

        $this->templating->expects($this->exactly(2))
            ->method('exists')
            ->willReturnOnConsecutiveCalls($exists1, $exists2);

        $this->listener->onKernelController($event);

        $this->assertEquals($expectedResult, $templateReference->getLogicalName());
    }

    public static function onKernelControllerProvider()
    {
        return array(
            array('cget', false, false, 'VlRadRestBundle:Default:cget.html.twig'),
            array('cget', false, true,  'AcmeDemoBundle:User:cget.html.twig'),
            array('cget', true,  true,  'AcmeDemoBundle:User:cget.html.twig'),

            array('get',  false, false, 'VlRadRestBundle:Default:get.html.twig'),
            array('get',  false, true,  'AcmeDemoBundle:User:get.html.twig'),
            array('get',  true,  true,  'AcmeDemoBundle:User:get.html.twig'),

            array('edit', false, false, 'VlRadRestBundle:Default:edit.html.twig'),
            array('edit', false, true,  'AcmeDemoBundle:User:edit.html.twig'),
            array('edit', true,  true,  'AcmeDemoBundle:User:edit.html.twig'),

            array('put',  false, false, 'VlRadRestBundle:Default:edit.html.twig'),
            array('put',  false, true,  'AcmeDemoBundle:User:edit.html.twig'),
            array('put',  true,  true,  'AcmeDemoBundle:User:put.html.twig'),

            array('new',  false, false, 'VlRadRestBundle:Default:new.html.twig'),
            array('new',  false, true,  'AcmeDemoBundle:User:new.html.twig'),
            array('new',  true,  true,  'AcmeDemoBundle:User:new.html.twig'),

            array('post', false, false, 'VlRadRestBundle:Default:new.html.twig'),
            array('post', false, true,  'AcmeDemoBundle:User:new.html.twig'),
            array('post', true,  true,  'AcmeDemoBundle:User:post.html.twig'),

            array('remove',  false, false, 'VlRadRestBundle:Default:remove.html.twig'),
            array('remove',  false, true,  'AcmeDemoBundle:User:remove.html.twig'),
            array('remove',  true,  true,  'AcmeDemoBundle:User:remove.html.twig'),

            array('delete', false, false, 'VlRadRestBundle:Default:remove.html.twig'),
            array('delete', false, true,  'AcmeDemoBundle:User:remove.html.twig'),
            array('delete', true,  true,  'AcmeDemoBundle:User:delete.html.twig'),
        );
    }

    /**
     * @dataProvider onKernelViewProvider
     */
    public function testOnKernelView($viewFormat, $requestFormat, $data, $extraData, $expected)
    {
        $request = new Request();
        if($requestFormat) {
            $request->setRequestFormat($requestFormat);
        }
        $view = View::create();
        if($viewFormat) {
            $view->setFormat($viewFormat);
        }
        $view->setData($data);
        $view->setExtraData($extraData);
        $event = $this->getControllerResultEvent($request, $view);

        $this->listener->onKernelView($event);

        $this->assertSame($expected, $view->getData());
    }

    public function onKernelViewProvider()
    {
        $class = new \stdClass();
        $array = array(new \stdClass(), new \stdClass());
        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->any())
            ->method('getData')
            ->willReturn($formData = new \stdClass());
        $form->expects($this->any())
            ->method('createView')
            ->willReturn($formView = $this->getMock('Symfony\Component\Form\FormView'));

        return array(
            array(null, null, $class, array('abc'=>'def'), array('data'=>$class, 'abc'=>'def')),
            array(null, 'html', $class, array('abc'=>'def'), array('data'=>$class, 'abc'=>'def')),
            array('html', null, $class, array('abc'=>'def'), array('data'=>$class, 'abc'=>'def')),
            array('html', 'json', $class, array('abc'=>'def'), array('data'=>$class, 'abc'=>'def')),
            array('json', 'html', $class, array('abc'=>'def'), $class),
            array(null, null, $form, array('abc'=>'def'), array('data'=>$formData, 'form'=>$formView, 'abc'=>'def')),
            array(null, 'html', $form, array('abc'=>'def'), array('data'=>$formData, 'form'=>$formView, 'abc'=>'def')),
            array('json', null, $form, array('abc'=>'def'), $form),
            array(null, null, array('form'=>$form, 'ghi'=>'jkl'), array('abc'=>'def'), array('form'=>$formView, 'ghi'=>'jkl', 'abc'=>'def')),
            array('xml', null, array('form'=>$form, 'ghi'=>'jkl'), array('abc'=>'def'), array('form'=>$form, 'ghi'=>'jkl')),
            array(null, null, $array, array('abc'=>'def'), array('data'=>$array, 'abc'=>'def')),
            array('xml', null, $array, array('abc'=>'def'), $array),
        );
    }

    public function testOnKernelViewWrongView()
    {
        $request = new Request();
        $view = $this->getMockBuilder('FOS\RestBundle\View\View')
            ->disableOriginalConstructor()
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $view->setFormat('html');
        $view->setData(new \stdClass());
        $event = $this->getControllerResultEvent($request, $view);

        $view->expects($this->never())->method($this->anything());

        $this->listener->onKernelView($event);
    }
}
