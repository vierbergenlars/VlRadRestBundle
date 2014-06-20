<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Manager;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType;
use Symfony\Component\Form\Forms;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager
 */
class FrontendManagerTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;
    private $authorizationChecker;
    private $formType;
    private $formFactory;

    public function setUp()
    {
        $this->authorizationChecker = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface');
        $this->resourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');
        $this->formType = new UserType();
        $this->formFactory = Forms::createFormFactoryBuilder()
        ->addExtension(new HttpFoundationExtension())
        ->addExtension(new ValidatorExtension(Validation::createValidator()))
        ->getFormFactory();
    }

    public function authenticationCheckProvider()
    {
        return array(
            // Prototype: ('auths'=>(mayList, mayView, mayCreate, mayEdit, mayDelete), method, expectException)
            array(
                array('mayList'=>false),
                'getList',
                true
            ),
            array(
                array('mayList'=>true),
                'getList',
                false
            ),
            array(
                array('mayView'=>false),
                'getResource',
                true
            ),
            array(
                array('mayView'=>true),
                'getResource',
                false
            ),
            array(
                array('mayCreate'=>false),
                'createResource',
                true
            ),
            array(
                array('mayCreate'=>true),
                'createResource',
                false
            ),
            array(
                array('mayView'=>false),
                'editResource',
                true
            ),
            array(
                array('mayView'=>true, 'mayEdit'=>false),
                'editResource',
                true
            ),
            array(
                array('mayView'=>true, 'mayEdit'=>true),
                'editResource',
                false
            ),
            array(
                array('mayView'=>false),
                'deleteResource',
                true
            ),
            array(
                array('mayView'=>true, 'mayDelete'=>false),
                'deleteResource',
                true
            ),
            array(
                array('mayView'=>true, 'mayDelete'=>true),
                'deleteResource',
                false
            ),
        );
    }

    private function setUpAuthenticationChecker($auths, $any = false)
    {
        foreach($auths as $fn=>$auth) {
            $this->authorizationChecker->expects($any?$this->any():$this->atLeastOnce())->method($fn)->will($this->returnValue($auth));
        }
        foreach(explode(' ', 'mayList mayView mayCreate mayEdit mayDelete') as $fn) {
            if(!array_key_exists($fn, $auths)) {
                $this->authorizationChecker->expects($this->never())->method($fn);
            }
        }
    }

    private function setUpResourceManager($withUser = true)
    {
        if($withUser) {
            $fakeUser = User::create('aaa', 1);
        } else {
            $fakeUser = null;
        }
        $this->resourceManager->expects($this->any())->method('find')->will($this->returnValue($fakeUser));
        $this->resourceManager->expects($this->any())->method('findAll')->will($this->returnValue($withUser?array($fakeUser):array()));
        $this->resourceManager->expects($this->any())->method('create')->will($this->returnValue($fakeUser));
        return $fakeUser;
    }

    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testGets($auths, $method, $expectException)
    {
        $this->setUpAuthenticationChecker($auths);
        $fakeUser = $this->setUpResourceManager();
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, $this->formType, $this->formFactory);
        if($expectException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array($fakeUser), $frontendManager->getList());
                break;
            case 'getResource':
                $this->assertSame($fakeUser, $frontendManager->getResource(1));
                break;
            case 'createResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->createResource(new Request()));
                break;
            case 'editResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->editResource(1, new Request()));
                break;
            case 'deleteResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->deleteResource(1, new Request()));
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }

    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testGetsNullRequest($auths, $method, $expectException)
    {
        $this->setUpAuthenticationChecker($auths);
        $fakeUser = $this->setUpResourceManager();
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, $this->formType, $this->formFactory);
        if($expectException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array($fakeUser), $frontendManager->getList());
                break;
            case 'getResource':
                $this->assertSame($fakeUser, $frontendManager->getResource(1));
                break;
            case 'createResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->createResource());
                break;
            case 'editResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->editResource(1));
                break;
            case 'deleteResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->deleteResource(1));
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }


    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testGetsNotFound($auths, $method, $expectAuthException)
    {
        $this->setUpAuthenticationChecker($auths, true);
        $fakeUser = $this->setUpResourceManager(false);
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, $this->formType, $this->formFactory);
        if($expectAuthException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array(), $frontendManager->getList());
                break;
            case 'getResource':
                $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                $frontendManager->getResource(1);
                break;
            case 'createResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->createResource(new Request()));
                break;
            case 'editResource':
                $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                $frontendManager->editResource(1, new Request());
                break;
            case 'deleteResource':
                $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                $frontendManager->deleteResource(1, new Request());
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }

    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testGetsNoFormFactory($auths, $method, $expectAuthException)
    {
        $this->setUpAuthenticationChecker($auths);
        $fakeUser = $this->setUpResourceManager();
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, $this->formType);
        if($expectAuthException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array($fakeUser), $frontendManager->getList());
                break;
            case 'getResource':
                $this->assertSame($fakeUser, $frontendManager->getResource(1));
                break;
            case 'createResource':
                if(!$expectAuthException) {
                    $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                }
                $frontendManager->createResource(new Request());
                break;
            case 'editResource':
                if(!$expectAuthException) {
                    $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                }
                $frontendManager->editResource(1, new Request());
                break;
            case 'deleteResource':
                if(!$expectAuthException) {
                    $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                }
                $frontendManager->deleteResource(1, new Request());
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }

    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testGetsNoForm($auths, $method, $expectAuthException)
    {
        $this->setUpAuthenticationChecker($auths);
        $fakeUser = $this->setUpResourceManager();
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, null, $this->formFactory);
        if($expectAuthException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array($fakeUser), $frontendManager->getList());
                break;
            case 'getResource':
                $this->assertSame($fakeUser, $frontendManager->getResource(1));
                break;
            case 'createResource':
                if(!$expectAuthException) {
                    $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                }
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->createResource(new Request()));
                break;
            case 'editResource':
                if(!$expectAuthException) {
                    $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                }
                $frontendManager->editResource(1, new Request());
                break;
            case 'deleteResource':
                $this->assertInstanceOf('Symfony\Component\Form\Form', $frontendManager->deleteResource(1, new Request()));
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }

    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testModify($auths, $method, $expectAuthException)
    {
        $this->setUpAuthenticationChecker($auths);
        $fakeUser = $this->setUpResourceManager();
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, $this->formType, $this->formFactory);
        if($expectAuthException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array($fakeUser), $frontendManager->getList());
                break;
            case 'getResource':
                $this->assertSame($fakeUser, $frontendManager->getResource(1));
                break;
            case 'createResource':
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add(array('user'=>array('username'=>'abc', 'email'=>'abc@example.com')));
                if(!$expectAuthException) {
                    $this->resourceManager->expects($this->once())->method('update')->with($fakeUser);
                } else {
                    $this->resourceManager->expects($this->never())->method('update');
                }
                $retval = $frontendManager->createResource($request);
                $this->assertInstanceOf('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', $retval);
                $this->assertEquals('abc', $retval->getUsername());
                break;
            case 'editResource':
                $request = new Request();
                $request->setMethod('PUT');
                $request->request->add(array('user'=>array('username'=>'abc', 'email'=>'abc@example.com')));
                if(!$expectAuthException) {
                    $this->resourceManager->expects($this->once())->method('update')->with($fakeUser);
                } else {
                    $this->resourceManager->expects($this->never())->method('update');
                }
                $retval = $frontendManager->editResource(1, $request);
                $this->assertInstanceOf('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', $retval);
                $this->assertEquals('abc', $retval->getUsername());
                break;
            case 'deleteResource':
                $request = new Request();
                $request->setMethod('DELETE');
                $request->request->add(array('form'=>array('submit'=>'')));
                if(!$expectAuthException) {
                    $this->resourceManager->expects($this->once())->method('delete')->with($fakeUser);
                } else {
                    $this->resourceManager->expects($this->never())->method('delete');
                }
                $retval = $frontendManager->deleteResource(1, $request);
                $this->assertNull($retval);
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }

    /**
     * @dataProvider authenticationCheckProvider
     */
    public function testModifyFail($auths, $method, $expectAuthException)
    {
        $this->setUpAuthenticationChecker($auths);
        $fakeUser = $this->setUpResourceManager();
        $frontendManager = new FrontendManager($this->resourceManager, $this->authorizationChecker, $this->formType, $this->formFactory);
        if($expectAuthException) {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AccessDeniedException');
        }

        switch($method) {
            case 'getList':
                $this->assertSame(array($fakeUser), $frontendManager->getList());
                break;
            case 'getResource':
                $this->assertSame($fakeUser, $frontendManager->getResource(1));
                break;
            case 'createResource':
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add(array('user'=>array('username'=>'')));
                $this->resourceManager->expects($this->never())->method('update');
                $retval = $frontendManager->createResource($request);
                $this->assertInstanceOf('Symfony\Component\Form\Form', $retval);
                break;
            case 'editResource':
                $request = new Request();
                $request->setMethod('PUT');
                $request->request->add(array('user'=>array('username'=>'')));
                $this->resourceManager->expects($this->never())->method('update');
                $retval = $frontendManager->editResource(1, $request);
                $this->assertInstanceOf('Symfony\Component\Form\Form', $retval);
                break;
            case 'deleteResource':
                $request = new Request();
                $request->setMethod('DELETE');
                $request->request->add(array());
                $this->resourceManager->expects($this->never())->method('delete');
                $retval = $frontendManager->deleteResource(1, $request);
                $this->assertInstanceOf('Symfony\Component\Form\Form', $retval);
                break;
            default:
                $this->fail('$frontendManager->'.$method.' should not be tested');
        }
    }
}
