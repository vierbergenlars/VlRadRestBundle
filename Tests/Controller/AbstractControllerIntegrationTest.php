<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security\AuthorizationChecker;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\CsrfProvider;
use vierbergenlars\Bundle\RadRestBundle\Controller\AbstractController;

abstract class AbstractControllerIntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $routeCollection;
    protected $router;
    protected $container;
    protected $resourceManager;

    protected function setUp()
    {
        $this->resourceManager = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\UserRepository')
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $routeCollection = $this->routeCollection = new RouteCollection();
        $this->router = new Router(new ClosureLoader(), function() use($routeCollection) {
            return $routeCollection;
        });
        $this->container = new ContainerBuilder();
        $this->container->set('router', $this->router);
        $this->container->set('service_container', $this->container);
        $this->container->set('resource_manager', $this->resourceManager);
        $this->container->set('form', new UserType());
        $this->container->set('form_factory', Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new CsrfExtension(new CsrfProvider()))
            ->getFormFactory()
        );

        $this->routeCollection->add('get_users', $this->route('/users', 'cget', 'GET'));
        $this->routeCollection->add('get_user', $this->route('/users/{id}', 'get', 'GET'));
        $this->routeCollection->add('new_user', $this->route('/users/new', 'new', 'GET'));
        $this->routeCollection->add('post_user', $this->route('/users', 'post', 'POST'));
        $this->routeCollection->add('edit_user', $this->route('/users/{id}/edit', 'edit', 'GET'));
        $this->routeCollection->add('put_user', $this->route('/users/{id}', 'put', 'PUT'));
        $this->routeCollection->add('patch_user', $this->route('/users/{id}', 'patch', 'PATCH'));
        $this->routeCollection->add('remove_user', $this->route('/users/{id}/remove', 'remove', 'GET'));
        $this->routeCollection->add('delete_user', $this->route('/users/{id}', 'delete', 'DELETE'));
    }

    protected function registerKnpPaginatorService()
    {
        if(!class_exists('Knp\Bundle\PaginatorBundle\Subscriber\SlidingPaginationSubscriber'))
            return $this->markTestSkipped('Knp Paginator bundle is not installed');
        if($this->container->hasDefinition('acme.demo.user.controller')) {
            $this->container->getDefinition('acme.demo.user.controller')
                ->addMethodCall('setPaginator',array(new Reference('knp_paginator')));
        }
        $this->container->register('knp_paginator')
            ->setClass('Knp\Component\Pager\Paginator')
            ->addArgument(new Reference('event_dispatcher'));
        $this->container->register('event_dispatcher')
            ->setClass('Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher')
            ->addArgument(new Reference('service_container'))
            ->addMethodCall('addSubscriber', array(new Reference('radrest.pagination.adapter.knp_paginator')))
            ->addMethodCall('addSubscriber', array(new Reference('knp_paginator.subscriber.sliding_pagination')));
        $this->container->register('radrest.pagination.adapter.knp_paginator')
            ->setClass('vierbergenlars\Bundle\RadRestBundle\Pagination\Adapters\KnpPaginationSubscriber');
        $this->container->register('knp_paginator.subscriber.sliding_pagination')
            ->setClass('Knp\Bundle\PaginatorBundle\Subscriber\SlidingPaginationSubscriber')
            ->addArgument(array(
                'defaultPaginationTemplate'=>'KnpPaginatorBundle:Pagination:sliding.html.twig',
                'defaultSortableTemplate'=>'KnpPaginatorBundle:Pagination:sortable_link.html.twig',
                'defaultFiltrationTemplate'=>'KnpPaginatorBundle:Pagination:filtration.html.twig',
                'defaultPageRange'=>5,
            ));
    }

    /**
     *
     * @param string $path
     * @param string $action
     * @param string $method
     * @return Route
     */
    abstract protected function route($path, $action, $method);
    /**
     *  @return AbstractController
     */
    abstract protected function createController();

    public function testRedirectTo()
    {
        $controller = $this->createController();

        if(!method_exists($controller,'_redirectTo')) {
            // This is not a required method on
            $this->markTestIncomplete('Missing %s::_redirectTo() (method for testing only)', get_class($controller));
        }
        $this->assertSame('get_users', $controller->_redirectTo('cget')->getRoute());
        $this->assertSame('get_user', $controller->_redirectTo('get', array('id'=>1))->getRoute());
    }

    public function testCGet()
    {
        $controller = $this->createController();
        $this->resourceManager->setFakeUsers(User::createArray(25));

        $request = new Request();

        $retval = $controller->cgetAction($request);
        $this->assertCount(10, $retval->getData());
        $this->assertSame(200, $retval->getStatusCode());
        $this->assertSame(array('abc', 'def'), $retval->getSerializationContext()->attributes->get('groups')->get());

        $request->query->set('page', 2);
        $retval2 = $controller->cgetAction($request);
        $this->assertCount(10, $retval->getData());
        $this->assertNotEquals($retval->getData(), $retval2->getData());

        $request->query->set('page', 3);
        $retval3 = $controller->cgetAction($request);
        $this->assertCount(5, $retval3->getData());
    }

    public function testGet()
    {
        $controller = $this->createController();
        $fakeUser = $this->resourceManager->setFakeUser(User::create('sfsf', 90));
        $this->resourceManager->expects($this->once())->method('find')->with(90);

        $retval = $controller->getAction(90);
        $this->assertSame($fakeUser, $retval->getData());
        $this->assertSame(200, $retval->getStatusCode());
        $this->assertSame(array('Default'), $retval->getSerializationContext()->attributes->get('groups')->get());
    }

    public function testNew()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create(''));
        $this->resourceManager->expects($this->once())->method('newInstance');

        $retval = $controller->newAction();
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertFalse($retval->getData()->isSubmitted());
        $this->assertSame(200, $retval->getStatusCode());
    }

    public function testPost()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('', 90));
        $this->resourceManager->expects($this->once())->method('newInstance');
        $this->resourceManager->expects($this->once())->method('create')->with($fakeUser);

        $request = new Request();
        $request->setMethod('POST');
        $request->request->add(array('user'=>array('username'=>'abc', 'email'=>'abc@example.com', '_token'=>'abcd')));
        $retval = $controller->postAction($request);

        $this->assertNotInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertSame('get_user', $retval->getRoute());
        $this->assertSame(array('id'=>90), $retval->getRouteParameters());
        $this->assertSame(201, $retval->getStatusCode());
    }

    public function testPostBad()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('', 90));
        $this->resourceManager->expects($this->once())->method('newInstance');
        $this->resourceManager->expects($this->never())->method('create');

        $request = new Request();
        $request->setMethod('POST');
        $request->request->add(array('user'=>array('username'=>'', 'email'=>'abc@example.com', '_token'=>'abcd')));
        $retval = $controller->postAction($request);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame(400, $retval->getStatusCode());
    }
    public function testEdit()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('sfsf', 90));
        $this->resourceManager->expects($this->once())->method('find');

        $retval = $controller->editAction(90);
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertFalse($retval->getData()->isSubmitted());
        $this->assertSame(200, $retval->getStatusCode());
    }

    public function testPut()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('defg', 90));
        $this->resourceManager->expects($this->once())->method('find');
        $this->resourceManager->expects($this->once())->method('update')->with($fakeUser);

        $request = new Request();
        $request->setMethod('PUT');
        $request->request->add(array('user'=>array('username'=>'abc', 'email'=>'abc@example.com', '_token'=>'abcd')));
        $retval = $controller->putAction($request, 90);

        $this->assertNotInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertSame('get_user', $retval->getRoute());
        $this->assertSame(array('id'=>90), $retval->getRouteParameters());
        $this->assertSame(204, $retval->getStatusCode());
    }

    public function testPutBad()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('defg', 90));
        $this->resourceManager->expects($this->once())->method('find');
        $this->resourceManager->expects($this->never())->method('update');


        $request = new Request();
        $request->setMethod('PUT');
        $request->request->add(array('user'=>array('username'=>'sfe', '_token'=>'abcd')));
        $retval = $controller->putAction($request, 90);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame(400, $retval->getStatusCode());
    }

    public function testPatch()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('defg', 90));
        $this->resourceManager->expects($this->once())->method('find');
        $this->resourceManager->expects($this->once())->method('update')->with($fakeUser);

        $request = new Request();
        $request->setMethod('PATCH');
        $request->request->add(array('user'=>array('username'=>'abc', '_token'=>'abcd')));
        $retval = $controller->patchAction($request, 90);

        $this->assertNotInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertSame('get_user', $retval->getRoute());
        $this->assertSame(array('id'=>90), $retval->getRouteParameters());
    }

    public function testPatchBad()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('defg', 90));
        $this->resourceManager->expects($this->once())->method('find');
        $this->resourceManager->expects($this->never())->method('update');

        $request = new Request();
        $request->setMethod('PATCH');
        $request->request->add(array('user'=>array('email'=>'abcef')));
        $retval = $controller->patchAction($request, 90);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame(400, $retval->getStatusCode());
    }

    public function testRemove()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('sfsf', 90));
        $this->resourceManager->expects($this->once())->method('find');

        $retval = $controller->removeAction(90);
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertFalse($retval->getData()->isSubmitted());
        $this->assertSame(200, $retval->getStatusCode());
    }

    public function testDelete()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('defg', 90));
        $this->resourceManager->expects($this->once())->method('find');
        $this->resourceManager->expects($this->once())->method('delete')->with($fakeUser);

        $request = new Request();
        $request->setMethod('DELETE');
        $request->request->add(array('form'=>array('submit'=>'', '_token'=>'abcd')));
        $retval = $controller->deleteAction($request, 90);

        $this->assertNotInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertSame('get_users', $retval->getRoute());
        $this->assertSame(array(), $retval->getRouteParameters());
        $this->assertSame(204, $retval->getStatusCode());
    }

    public function testDeleteBad()
    {
        $controller = $this->createController();

        $fakeUser = $this->resourceManager->setFakeUser(User::create('defg', 90));
        $this->resourceManager->expects($this->once())->method('find');
        $this->resourceManager->expects($this->never())->method('delete');

        $request = new Request();
        $request->setMethod('DELETE');
        $request->request->add(array('form'=>array()));
        $retval = $controller->deleteAction($request, 90);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame(400, $retval->getStatusCode());
    }
}
