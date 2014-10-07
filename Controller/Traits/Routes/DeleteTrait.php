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
use Symfony\Component\Form\FormFactoryInterface;

/**
 * This trait provides routes for resource deletion
 */
trait DeleteTrait
{
    use AbstractBaseManipulateTrait;

    /**
     * @return FormFactoryInterface
     */
    abstract protected function getFormFactory();

    protected function createDeleteForm($object)
    {
        return $this->getFormFactory()
            ->createBuilder('form', $object, array('data_class'=>get_class($object)))
            ->add('submit', 'submit')
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @AView
     */
    public function removeAction($id)
    {
        $object = $this->getResourceManager()->find($id);
        $form = $this->createDeleteForm($object);
        $view = View::create($form);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc
     * @AView
     */
    public function deleteAction(Request $request, $id)
    {
        $object = $this->getResourceManager()->find($id);
        $form = $this->createDeleteForm($object);
        if($this->processForm($form, $request)) {
            $view = $this->redirectTo('cget')->setStatusCode(Codes::HTTP_NO_CONTENT);
        } else {
            $view = View::create($form, Codes::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
