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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security\AuthorizationChecker;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\CsrfTokenManager;

class RadRestControllerTest extends \PHPUnit_Framework_TestCase
{
    private $frontendManager;
    private $routeCollection;
    private $router;
    private $container;
    private $resourceManager;

    public function setUp()
    {
        $this->frontendManager = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager')
        ->setConstructorArgs($this->createFrontendManagerArgs())
        ->enableProxyingToOriginalMethods()
        ->getMock();
        $routeCollection = $this->routeCollection = new RouteCollection();
        $this->router = new Router(new ClosureLoader(), function() use($routeCollection) {
            return $routeCollection;
        });
        $this->container = new Container();
        $this->container->set('router', $this->router);

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

    private function createFrontendManagerArgs()
    {
        $this->resourceManager = new UserRepository();

        return array(
            $this->resourceManager,
            new AuthorizationChecker(),
            new UserType(),
            Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new CsrfExtension(new CsrfTokenManager()))
            ->getFormFactory()
        );
    }

    private function route($path, $action, $method = null)
    {
        $route = new Route($path, array('_controller'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController::'.$action.'Action'));
        $route->setMethods($method);
        return $route;
    }

    public function testRedirectTo()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);

        $this->assertSame('get_users', $controller->_redirectTo('cget')->getRoute());
        $this->assertSame('get_user', $controller->_redirectTo('get', array('id'=>1))->getRoute());
    }

    public function testCGet()
    {
        $controller = new UserController();
        $controller->setFrontendManager($this->frontendManager);
        $fakeUser = $this->resourceManager->fakeUser = User::create('aafs', 5);

        $retval = $controller->cgetAction();
        $this->assertSame(array($fakeUser), $retval->getData());
        $this->assertSame('data', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
        $this->assertSame(array('abc', 'def'), $retval->getSerializationContext()->attributes->get('groups')->get());
    }

    public function testGet()
    {
        $controller = new UserController();
        $controller->setFrontendManager($this->frontendManager);
        $fakeUser = $this->resourceManager->fakeUser = User::create('sfsf', 90);
        $this->frontendManager->expects($this->once())->method('getResource')->with(90);

        $retval = $controller->getAction(90);
        $this->assertSame($fakeUser, $retval->getData());
        $this->assertSame('data', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
        $this->assertSame(array('Default'), $retval->getSerializationContext()->attributes->get('groups')->get());
    }

    public function testNew()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('');
        $this->frontendManager->expects($this->once())->method('createResource');

        $retval = $controller->newAction();
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertFalse($retval->getData()->isSubmitted());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
    }

    public function testPost()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('', 90);
        $this->frontendManager->expects($this->once())->method('createResource');

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
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('', 90);
        $this->frontendManager->expects($this->once())->method('createResource');

        $request = new Request();
        $request->setMethod('POST');
        $request->request->add(array('user'=>array('username'=>'', 'email'=>'abc@example.com', '_token'=>'abcd')));
        $retval = $controller->postAction($request);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(400, $retval->getStatusCode());
    }
    public function testEdit()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('sfsf', 90);
        $this->frontendManager->expects($this->once())->method('editResource');

        $retval = $controller->editAction(90);
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertFalse($retval->getData()->isSubmitted());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
    }

    public function testPut()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('defg', 90);
        $this->frontendManager->expects($this->once())->method('editResource');

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
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('defg', 90);
        $this->frontendManager->expects($this->once())->method('editResource');

        $request = new Request();
        $request->setMethod('PUT');
        $request->request->add(array('user'=>array('username'=>'sfe', '_token'=>'abcd')));
        $retval = $controller->putAction($request, 90);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(400, $retval->getStatusCode());
    }
    
    public function testPatch()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('defg', 90);
        $this->frontendManager->expects($this->once())->method('editResource');

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
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);
    
        $fakeUser = $this->resourceManager->fakeUser = User::create('defg', 90);
        $this->frontendManager->expects($this->once())->method('editResource');
    
        $request = new Request();
        $request->setMethod('PATCH');
        $request->request->add(array('user'=>array('email'=>'abcef')));
        $retval = $controller->patchAction($request, 90);
    
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(400, $retval->getStatusCode());
    }
    
    public function testRemove()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('sfsf', 90);
        $this->frontendManager->expects($this->once())->method('deleteResource');

        $retval = $controller->removeAction(90);
        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertFalse($retval->getData()->isSubmitted());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
    }

    public function testDelete()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('defg', 90);
        $this->frontendManager->expects($this->once())->method('deleteResource');

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
        $controller = new UserController();
        $controller->setContainer($this->container);
        $controller->setFrontendManager($this->frontendManager);

        $fakeUser = $this->resourceManager->fakeUser = User::create('defg', 90);
        $this->frontendManager->expects($this->once())->method('deleteResource');

        $request = new Request();
        $request->setMethod('DELETE');
        $request->request->add(array('form'=>array()));
        $retval = $controller->deleteAction($request, 90);

        $this->assertInstanceOf('Symfony\Component\Form\Form', $retval->getData());
        $this->assertTrue($retval->getData()->isSubmitted());
        $this->assertFalse($retval->getData()->isValid());
        $this->assertSame('form', $retval->getTemplateVar());
        $this->assertSame(400, $retval->getStatusCode());
    }
}
