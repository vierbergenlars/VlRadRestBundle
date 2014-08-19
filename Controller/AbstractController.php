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

use FOS\RestBundle\Controller\Annotations\View as AView;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

abstract class AbstractController implements ClassResourceInterface, RadRestControllerInterface
{
    /**
     * Gets the name of the route for the given action on this controller.
     * @param string $action
     * @return string The route name
     */
    abstract protected function getRouteName($action);

    /**
     * Gets a slice of the page description for one page
     * @param PageDescriptionInterface $pageDescription
     * @param int $page
     * @return array<object>
     */
    protected function getPagination(PageDescriptionInterface $pageDescription, $page)
    {
        return $pageDescription->getSlice($page<1?0:($page-1)*10, 10);
    }

    /**
     * Handles the view before it is returned
     * @param View $view
     */
    protected function handleView(View $view)
    {
        return $view;
    }

    /**
     * Redirects to another action on the same controller
     * @param string $nextAction The action name to redirect to
     * @param array<string> $params Parameters to pass to the route generator
     * @return View
     */
    protected function redirectTo($nextAction, array $params = array())
    {
        return View::createRouteRedirect($this->getRouteName($nextAction), $params);
    }

    /**
     * Returns a list of serializer groups for the given action on this controller
     *
     * @codeCoverageIgnore
     * @param string $action
     * @return array<string>|null Serialization groups for this action
     */
    public function getSerializationGroups($action)
    {
        return null;
    }

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function cgetAction(Request $request)
    {
        $list = $this->getFrontendManager()->getList(true);
        if(is_array($list)) {
            $view = View::create($list);
        } else {
            $view = View::create($this->getPagination($list, $request->query->get('page', 1)));
        }
        $view->getSerializationContext()->setGroups($this->getSerializationGroups('cget')?:array('Default'));
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
        $view->getSerializationContext()->setGroups($this->getSerializationGroups('get')?:array('Default'));
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
