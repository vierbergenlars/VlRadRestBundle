<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Manager;

use vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;

/**
 *
 */
class FrontendManager
{
    /**
     *
     * @var ResourceManagerInterface
     */
    private $resourceManager;

    /**
     *
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     *
     * @var AbstractType|null
     */
    private $formType;

    /**
     *
     * @var FormFactoryInterface|null
     */
    private $formFactory;

    /**
     *
     * @param ResourceManagerInterface $resourceManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AbstractType $formType
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(ResourceManagerInterface $resourceManager, AuthorizationCheckerInterface $authorizationChecker, AbstractType $formType = null, FormFactoryInterface $formFactory = null)
    {
        $this->resourceManager = $resourceManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->formType = $formType;
        $this->formFactory = $formFactory;
    }

    /**
     * Gets a list of all resources of this type
     * @throws AccessDeniedException When access to list the resources is disallowed by the AuthorizationChecker
     * @return array
     */
    public function getList()
    {
        if(!$this->authorizationChecker->mayList()) {
            throw new AccessDeniedException();
        }

        return $this->resourceManager->findAll();
    }

    /**
     * Gets a resource by its id
     * @param mixed $id The id of the object
     * @throws NotFoundHttpException When the resource does not exists
     * @throws AccessDeniedException When access to this resource is disallowed by the AuthorizationChecker
     * @return object The object representing the resource
     */
    public function getResource($id)
    {
        $object = $this->resourceManager->find($id);

        if($object === null) {
            throw new NotFoundHttpException();
        }

        if(!$this->authorizationChecker->mayView($object)) {
            throw new AccessDeniedException();
        }

        return $object;
    }

    /**
     * Internally handles resource creation & modification from a form
     * @param object $object The object to modify
     * @param Request $request HTTP Request
     * @param string $method HTTP method the form should be submitted with
     * @throws NotFoundHttpException When the form or the form factory do not exist
     * @return Form|object Returns the modified object when the resource has been updated, or a Form instance when it has not yet been updated
     */
    private function handleResourceForm($object, Request $request, $method)
    {
        if(!$this->formType||!$this->formFactory) {
            throw new NotFoundHttpException();
        }

        $form = $this->formFactory->createBuilder($this->formType, $object)
        ->setMethod($method)
        ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->resourceManager->update($object);
            return $object;
        }

        return $form;

    }

    /**
     * Creates a new resource, or shows a form to create a new resource
     * @param Request $request Current HTTP request
     * @throws AccessDeniedException When creating a new resource is disallowed by the AuthorizationChecker
     * @return Form|object Returns the created object when the form has been submitted, and it was valid. Return a Form when no form has yet been submitted, or the submitted form was invalid.
     */
    public function createResource(Request $request)
    {
        if(!$this->authorizationChecker->mayCreate()) {
            throw new AccessDeniedException();
        }

        $object = $this->resourceManager->create();

        return $this->handleResourceForm($object, $request, 'POST');
    }

    /**
     * Modifies an existing resource, or shows a prepopulated form to modify an existing resource
     * @param mixed $id The id of the resource to modify
     * @param Request $request HTTP Request
     * @throws AccessDeniedException When modifying this resource is disallowed by the AuthorizationChecker
     * @return Form|object Returns the modified object when the form has been submitted, and it was valid. Return a Form when no form has yet been submitted, or the submitted form was invalid.
     */
    public function editResource($id, Request $request)
    {
        $object = $this->getResource($id);

        if(!$this->authorizationChecker->mayEdit($object)) {
            throw new AccessDeniedException();
        }

        return $this->handleResourceForm($object, $request, 'PUT');
    }

    /**
     * Deletes an existing resource, or shows a form to delete the resource
     * @param mixed $id The id of the resource to delete
     * @param Request $request HTTP Request
     * @throws NotFoundHttpException When the form factory do not exist
     * @throws AccessDeniedException When deleting this resource is disallowed by the AuthorizationChecker
     * @return boolean|Form Returns true when the resource has been deleted. Returns a confirmation
     */
    public function deleteResource($id, Request $request)
    {
        $object = $this->getResource($id);

        if(!$this->authorizationChecker->mayDelete($object)) {
            throw new AccessDeniedException();
        }

        if(!$this->formFactory) {
            throw new NotFoundHttpException();
        }

        $deleteForm = $this->formFactory->createBuilder()
        ->setMethod('DELETE')
        ->add('submit')
        ->getForm();

        $deleteForm->handleRequest($request);

        if($deleteForm->isValid()) {
            $this->resourceManager->delete($object);
            return true;
        }

        return $deleteForm;
    }
}