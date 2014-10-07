<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes;

use FOS\RestBundle\Controller\Annotations\View as AView;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\View\View;

/**
 * This trait provides routes for resource modification
 */
trait EditTrait
{
    use AbstractBaseManipulateTrait;

    protected function createEditForm($object)
    {
        return $this->createForm($object, 'PUT');
    }

    /**
     * @AView
     */
    public function editAction($id)
    {
        $object = $this->getResourceManager()->find($id);
        $form = $this->createEditForm($object);
        $view = View::create($form);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function putAction(Request $request, $id)
    {
        $object = $this->getResourceManager()->find($id);
        $form = $this->createEditForm($object);
        if($this->processForm($form, $request)) {
            $view = $this->redirectTo('get', array('id'=>$object->getId()))->setStatusCode(Codes::HTTP_NO_CONTENT);
        } else {
            $view = View::create($form, Codes::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
