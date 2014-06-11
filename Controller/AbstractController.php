<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as AView;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Form\Form;
use FOS\RestBundle\Routing\ClassResourceInterface;

abstract class AbstractController implements ClassResourceInterface, RadRestControllerInterface
{
    /**
     * Redirects to another action on the same controller
     * @param string $nextAction The action name to redirect to
     * @param array<string> $params Parameters to pass to the route generator
     * @return View
     */
    abstract protected function redirectTo($nextAction, array $params = array());

    /**
     * Gets the frontend manager for this resource
     * @return FrontendManager
     */
    abstract public function getFrontendManager();

    /**
     * Handles the view before it is returned
     * @param View $view
     */
    abstract protected function handleView(View $view);

    /**
     * @param string $type
     */
    private function getSerializationGroup($type)
    {
        $sg = (array)$this->getSerializationGroups();
        return isset($sg[$type])?$sg[$type]:array('Default');
    }

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function cgetAction()
    {
        $view = View::create($this->getFrontendManager()->getList());
        $view->getSerializationContext()->setGroups($this->getSerializationGroup('list'));
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function getAction($id)
    {
        $object = $this->getFrontendManager()->getResource($id);
        $view   = View::create($object);
        $view->getSerializationContext()->setGroups($this->getSerializationGroup('object'));
        return $this->handleView($view);
    }

    /**
     * @AView
     */
    public function newAction()
    {
        $form = $this->getFrontendManager()->createResource();
        $view = View::create($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function postAction(Request $request)
    {
        $ret = $this->getFrontendManager()->createResource($request);

        if($ret instanceof Form) {
            $view = View::create($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('get', array('id'=>$ret->getId()))->setStatusCode(Codes::HTTP_CREATED);
        }

        return $this->handleView($view);
    }

    /**
     * @AView
     */
    public function editAction($id)
    {
        $form = $this->getFrontendManager()->editResource($id);
        $view = View::create($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function putAction(Request $request, $id)
    {
        $ret = $this->getFrontendManager()->editResource($id, $request);

        if($ret instanceof Form) {
            $view = View::create($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('get', array('id'=>$ret->getId()))->setStatusCode(Codes::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function patchAction(Request $request, $id)
    {
        $ret = $this->getFrontendManager()->editResource($id, $request, true);

        if($ret instanceof Form) {
            $view = View::create($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('get', array('id'=>$ret->getId()))->setStatusCode(Codes::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }

    /**
     * @AView
     */
    public function removeAction($id)
    {
        $form = $this->getFrontendManager()->deleteResource($id);
        $view = View::create($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function deleteAction(Request $request, $id)
    {
        $ret = $this->getFrontendManager()->deleteResource($id, $request);

        if($ret instanceof Form) {
            $view = View::create($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('cget')->setStatusCode(Codes::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }
}