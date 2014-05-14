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
use Symfony\Component\Form\AbstractType;
use vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface;
use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Util\Codes;

/**
 * Base Controller for Controllers using the RAD Rest functionality
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */

class RadRestController extends FOSRestController implements ClassResourceInterface
{
	/**
	 *
	 * @var AbstractType|null
	 */
	private $formType = null;

	/**
	 *
	 * @var AuthorizationCheckerInterface
	 */
	private $authorizationChecker;

	/**
	 *
	 * @var ResourceManagerInterface
	 */
	private $resourceManager;

	public function setFormType(AbstractType $formType = null)
	{
		$this->formType = $formType;
	}

	public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->authorizationChecker = $authorizationChecker;
	}

	public function setResourceManager(ResourceManagerInterface $resourceManager)
	{
		$this->resourceManager = $resourceManager;
	}

	/**
	 * Redirects to another action on the same controller
	 * @param string $nextAction The action name to redirect to
	 * @param array $params Parameters to pass to the route generator
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function redirectTo($nextAction, array $params = array())
	{
		$controller = get_class().'::'.$nextAction.'Action';
		$routes = $this->get('router')->getRouteCollection()->all();
		// FIXME: Get rid of O(n) performance on routes
		foreach($routes as $routeName => $route)
		{
			if($route->hasDefault('_controller')&&$route->getDefault('_controller') == $controller)
				break;
		}

		$view = $this->routeRedirectView($routeName, $params);
		return $this->handleView($view);
	}

	/**
	 * Create a form for this resource
	 * @param object $data The data to prepopulate the form with
	 * @throws NotFoundHttpException
	 * @return \Symfony\Component\Form\Form
	 */
	private function getForm($data = null)
	{
		if($this->formType === null) {
			throw $this->createNotFoundException();
		}

		return $this->createForm($this->formType, $data);
	}

	private function getObject($cname)
	{
		$object = $this->resourceManager->findByCanonicalName($cname);

		if(!$object) {
			throw $this->createNotFoundException();
		}

		if(!$this->authorizationChecker->mayView($object)) {
			throw new AccessDeniedException();
		}

		return $object;
	}

	public function cgetAction()
	{
		if(!$this->authorizationChecker->mayList()) {
			throw new AccessDeniedException();
		}

		$view = $this->view($this->resourceManager->findAll())->setTemplateVar('list');
		return $this->handleView($view);
	}

	public function getAction($cname)
	{
		$object = $this->getObject($cname);
		$view = $this->view($object);
		return $this->handleView($view);
	}

	public function newAction()
	{
		if(!$this->authorizationChecker->mayCreate()) {
			throw new AccessDeniedException();
		}

		$object = $this->resourceManager->create();
		$form = $this->getForm($object);
		$view = $this->view($form)->setTemplateVar('form');
		return $this->handleView($view);
	}

	public function postAction(Request $request)
	{
		if(!$this->authorizationChecker->mayCreate()) {
			throw new AccessDeniedException();
		}

		$object = $this->resourceManager->create();
		$form = $this->getForm($object);
		$form->handleRequest($request);

		if($form->isValid()) {
			$this->resourceManager->update($object);
			return $this->redirectTo('get', array('cname'=>$this->resourceManager->getCanonicalName($object)));
		}

		$view = $this->view($form, Codes::HTTP_BAD_REQUEST)->setTemplate('form');
		return $this->handleView($view);
	}

	public function editAction($cname)
	{
		$object = $this->getObject($cname);

		if(!$this->authorizationChecker->mayEdit($object)) {
			throw new AccessDeniedException();
		}

		$form = $this->getForm($object);

		$view = $this->view($form)->setTemplateVar('form');
		return $this->handleView($view);
	}

	public function putAction(Request $request, $cname)
	{
		$object = $this->getObject($cname);

		if(!$this->authorizationChecker->mayEdit($object)) {
			throw new AccessDeniedException();
		}

		$form = $this->getForm($object);
		$form->submit($request);

		if($form->isValid()) {
			$this->resourceManager->update($object);
			return $this->redirectTo('get', array('cname'=>$this->resourceManager->getCanonicalName($object)));
		}

		$view = $this->view($form, Codes::HTTP_BAD_REQUEST)->setTemplateVar('form');
		return $this->handleView($view);
	}

	public function removeAction($cname)
	{
		$object = $this->getObject($cname);

		if(!$this->authorizationChecker->mayDelete($object)) {
			throw new AccessDeniedException();
		}

		$view = $this->view($object);
		return $this->handleView($view);
	}

	public function deleteAction($cname)
	{
		$object = $this->getObject($cname);

		if(!$this->authorizationChecker->mayDelete($object)) {
			throw new AccessDeniedException();
		}

		$this->resourceManager->delete($object);
		return $this->redirectTo('cget');

	}
}
