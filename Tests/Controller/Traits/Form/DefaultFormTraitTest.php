<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Form;

use Symfony\Component\Form\Forms;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form\UserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Form\DefaultFormTrait
 * @covers vierbergenlars\Bundle\RadRestBundle\View\View
 */
class DefaultFormTraitTest extends \PHPUnit_Framework_TestCase
{
    private $resourceManager;
    private $formFactory;
    private $formTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->getFormFactory();
        $this->resourceManager = $this->getMock('vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface');

        if(!trait_exists(__NAMESPACE__.'\\_DefaultFormTrait', false)) {
            // Expose DefaultPaginationTrait::getPagination as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Form\DefaultFormTrait;
                trait _DefaultFormTrait {
                    use DefaultFormTrait {
                        createForm as public;
                        processForm as public;
                    }
                }'
            );
        }

        $this->formTrait = $this->getMockBuilder(__NAMESPACE__.'\\_DefaultFormTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();
        $this->formTrait->expects($this->any())
            ->method('getFormFactory')
            ->willReturn($this->formFactory);
        $this->formTrait->expects($this->any())
            ->method('getResourceManager')
            ->willReturn($this->resourceManager);
        $this->formTrait->expects($this->any())
            ->method('getFormType')
            ->willReturn(new UserType());
    }

    public function testCreateForm()
    {
        $form = $this->formTrait->createForm($user = User::create('abc'), 'PUT');
        $this->assertTrue($form instanceof Form);
        $this->assertSame($user, $form->getData());
        $this->assertSame('PUT', $form->getConfig()->getMethod());
    }

    /**
     * @dataProvider processFormDataProvider
     */
    public function testProcessForm($httpMethod, $resourceManagerMethod, $result)
    {
        $request = new Request();
        $request->setMethod($httpMethod);
        $request->request->add(array('user'=> array(
            'username' => 'cde',
            'email' => 'aaa@example.com',
        )));
        $form = $this->formTrait->createForm($user = User::create('abc'), $httpMethod);

        $this->resourceManager->expects($this->once())
            ->method($resourceManagerMethod)
            ->with($user);
        $this->assertEquals($result, $this->formTrait->processForm($form, $request));
    }

    public function processFormDataProvider()
    {
        return array(
            array('POST', 'create', true),
            array('PUT', 'update', true),
            array('PATCH', 'update', true),
        );
    }

    public function testProcessFormInvalidMethod()
    {
        $request = new Request();
        $request->setMethod('PUT');
        $request->request->add(array('user'=> array(
            'username' => 'cde',
            'email' => 'aaa@example.com',
        )));
        $form = $this->formTrait->createForm($user = User::create('abc'), 'POST');

        $this->assertFalse($this->formTrait->processForm($form, $request));
    }

    public function testProcessFormDelete()
    {
        $request = new Request();
        $request->setMethod('DELETE');
        $request->request->add(array('form'=> array('submit'=>'submit')));
        $user = User::create('abc');
        $form = $this->formFactory
            ->createBuilder('form', $user, array('data_class'=>get_class($user)))
            ->setMethod('DELETE')
            ->add('submit', 'submit')
            ->getForm();

        $this->resourceManager->expects($this->once())
            ->method('delete')
            ->with($user);
        $this->assertTrue($this->formTrait->processForm($form, $request));
    }


}
