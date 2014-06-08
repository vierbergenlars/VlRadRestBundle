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
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;

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

    public function getFrontendManager()
    {
        return $this->frontendManager;
    }

    /**
     * Returns a list of serializer groups for each type of GET request (list & single object view)
     * @codeCoverageIgnore
     * @return array
     */
    public function getSerializationGroups()
    {
        return array(
            'list'   => array('Default'),
            'object' => array('Default'),
        );
    }
    
    /**
     * @param string $type
     */
    private function getSerializationGroup($type)
    {
        $sg = (array)$this->getSerializationGroups();
        return isset($sg[$type])?$sg[$type]:array('Default');
    }

    /**
     * Redirects to another action on the same controller
     * @param string $nextAction The action name to redirect to
     * @param array<string> $params Parameters to pass to the route generator
     * @return View
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
        
        // @codeCoverageIgnoreStart
        throw new \LogicException('No route found for controller '.$controller);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function cgetAction()
    {
        $view = $this->view($this->frontendManager->getList());
        $view->getSerializationContext()->setGroups($this->getSerializationGroup('list'));
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function getAction($id)
    {
        $object = $this->frontendManager->getResource($id);
        $view = $this->view($object);
        $view->getSerializationContext()->setGroups($this->getSerializationGroup('object'));
        return $this->handleView($view);
    }

    /**
     * @AView
     */
    public function newAction()
    {
        $form = $this->frontendManager->createResource();
        $view = $this->view($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function postAction(Request $request)
    {
        $ret = $this->frontendManager->createResource($request);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
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
        $form = $this->frontendManager->editResource($id);
        $view = $this->view($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function putAction(Request $request, $id)
    {
        $ret = $this->frontendManager->editResource($id, $request);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
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
        $ret = $this->frontendManager->editResource($id, $request, true);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
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
        $form = $this->frontendManager->deleteResource($id);
        $view = $this->view($form)->setTemplateVar('form');
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function deleteAction(Request $request, $id)
    {
        $ret = $this->frontendManager->deleteResource($id, $request);

        if($ret instanceof Form) {
            $view = $this->view($ret, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
        } else {
            $view = $this->redirectTo('cget')->setStatusCode(Codes::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }
}
