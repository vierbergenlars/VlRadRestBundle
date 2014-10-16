<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Form;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use vierbergenlars\Bundle\RadRestBundle\Manager\ResourceManagerInterface;
use Symfony\Component\HttpFoundation\Request;

trait DefaultFormTrait
{
    /**
     * @return FormTypeInterface
     */
    abstract public function getFormType();

    /**
     * @return FormFactoryInterface
     */
    abstract protected function getFormFactory();

    /**
     * @return ResourceManagerInterface
     */
    abstract public function getResourceManager();

    /**
     * Creates a new form object
     *
     * @param object $object
     * @param string $method
     * @return FormInterface
     */
    protected function createForm($object, $method)
    {
        return $this->getFormFactory()
            ->createBuilder($this->getFormType(), $object)
            ->setMethod($method)
            ->getForm();
    }

    /**
     * Processes a submitted form
     *
     * @param FormInterface $form
     * @param Request $request
     * @return bool Whether the form was processed successfully or not
     */
    protected function processForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if($form->isValid()) {
            switch($form->getConfig()->getMethod()) {
                case 'POST':
                    $this->getResourceManager()->create($form->getData());
                    break;
                case 'PUT':
                case 'PATCH':
                    $this->getResourceManager()->update($form->getData());
                    break;
                case 'DELETE':
                    $this->getResourceManager()->delete($form->getData());
                    break;
                default:
                    return false;
            }
            return true;
        }
        return false;
    }
}
