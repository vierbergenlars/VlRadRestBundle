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

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Util\Codes;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use Symfony\Component\Form\Form;
use FOS\RestBundle\View\View;

/**
 * Base Controller for Controllers using the RAD Rest functionality
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class RadRestController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var FrontendManager
     */
    private $frontendManager;

    public function setFrontendManager(FrontendManager $frontendManager)
    {
        $this->frontendManager = $frontendManager;
    }

    /**
     * Redirects to another action on the same controller
     * @param string $nextAction The action name to redirect to
     * @param array $params Parameters to pass to the route generator
     * @return View|null
     */
    protected function redirectTo($nextAction, array $params = array())
    {
        $controller = get_class($this).'::'.$nextAction.'Action';
        $routes = $this->get('router')->getRouteCollection()->all();
        // FIXME: Get rid of O(n) performance on routes
        foreach($routes as $routeName => $route)
        {
            if($route->hasDefault('_controller')&&$route->getDefault('_controller') == $controller) {
                return $this->routeRedirectView($routeName, $params);
            }
        }
        return null;
    }

    public function cgetAction()
    {
        $view = $this->view($this->frontendManager->getList());
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $object = $this->frontendManager->getResource($id);
        $view = $this->view($object);
        return $this->handleView($view);
    }

    public function newAction()
    {
        $form = $this->frontendManager->createResource(new Request());
        $view = $this->view($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        $ret = $this->frontendManager->createResource($request);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('get', array('id'=>$ret->getId()));
        }

        return $this->handleView($view);
    }

    public function editAction($id)
    {
        $form = $this->frontendManager->editResource($id,new Request());
        $view = $this->view($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    public function putAction(Request $request, $id)
    {
        $ret = $this->frontendManager->editResource($id, $request);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('get', array('id'=>$ret->getId()));
        }

        return $this->handleView($view);
    }

    public function removeAction($id)
    {
        $form = $this->frontendManager->deleteResource($id, new Request());
        $view = $this->view($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    public function deleteAction(Request $request, $id)
    {
        $ret = $this->frontendManager->deleteResource($id, $request);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('cget');
        }

        return $this->handleView($view);
    }
}
