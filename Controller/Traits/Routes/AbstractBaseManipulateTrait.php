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

use vierbergenlars\Bundle\RadRestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base trait for all routes that manipulate data
 */
trait AbstractBaseManipulateTrait
{
    use AbstractBaseTrait;

    /**
     * Redirects to another action on the same controller
     * @param string $nextAction The action name to redirect to
     * @param array<string> $params Parameters to pass to the route generator
     * @return View
     */
    abstract protected function redirectTo($nextAction, array $params = array());

    /**
     * Creates a new form object
     *
     * @param object $object
     * @param string $method
     * @return FormInterface
     */
    abstract protected function createForm($object, $method);

    /**
     * Processes a submitted form
     *
     * @param FormInterface $form
     * @param Request $request
     * @return bool Whether the form was processed successfully or not
     */
    abstract protected function processForm(FormInterface $form, Request $request);
}
