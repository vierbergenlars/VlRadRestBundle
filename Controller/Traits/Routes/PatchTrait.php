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
 * This trait provides routes for PATCH on a resource
 */
trait PatchTrait
{
    use AbstractBaseManipulateTrait;

    protected function createPatchForm($object)
    {
        return $this->createForm($object, 'PATCH');
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function patchAction(Request $request, $id)
    {
        $object = $this->getResourceManager()->find($id);
        $form = $this->createPatchForm($object);
        if($this->processForm($form, $request)) {
            $view = $this->redirectTo('get', array('id'=>$object->getId()))->setStatusCode(Codes::HTTP_NO_CONTENT);
        } else {
            $view = View::create($form, Codes::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
