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
 * This trait provides routes for resource creation
 */
trait CreateTrait
{
    use AbstractBaseManipulateTrait;

    protected function createCreateForm()
    {
        return $this->createForm($this->getResourceManager()->newInstance(), 'POST');
    }

    /**
     * @AView
     */
    public function newAction()
    {
        $form = $this->createCreateForm();
        $view = View::create($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function postAction(Request $request)
    {
        $form = $this->createCreateForm();
        if($this->processForm($form, $request)) {
            $view = $this->redirectTo('get', array('id' => $form->getData()->getId()))->setStatusCode(Codes::HTTP_CREATED);
        } else {
            $view = View::create($form, Codes::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
