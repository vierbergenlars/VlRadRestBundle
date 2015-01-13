<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\Routing\Route;
use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface;

abstract class AbstractRadRestHandler implements HandlerInterface
{
    const BASE_CONTROLLER_CLASS = 'vierbergenlars\Bundle\RadRestBundle\Controller\AbstractController';

    /**
     * Checks if the route is supported by the handler
     * @param Route $route
     * @return boolean
     */
    abstract protected function isSupported(Route $route);

    /**
     *
     * @param Route $route
     * @return RadRestControllerInterface
    */
    abstract protected function getControllerInstance(Route $route);

    public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $reflMethod)
    {
        if(!$this->isSupported($route)) {
            return;
        }

        // The handler does not process overridden methods.
        if($reflMethod->getDeclaringClass()->getName() !== static::BASE_CONTROLLER_CLASS) {
            return;
        }

        $controllerInst  = $this->getControllerInstance($route);
        $resourceManager = $controllerInst->getResourceManager();
        $formType        = $controllerInst->getFormType();

        switch($reflMethod->getName()) {
            case 'putAction':
            case 'postAction':
            case 'patchAction':
                if($formType !== null) {
                    $this->setObjectProperty($annotation, 'input', get_class($formType));
                }
                break;
            case 'getAction':
                $this->setObjectProperty($annotation, 'output', array(
                'class'=>get_class($resourceManager->newInstance()),
                'groups'=>$controllerInst->getSerializationGroups('get'),
                ));
                break;
            case 'cgetAction':
                $this->setObjectProperty($annotation, 'output', array(
                'class'=>get_class($resourceManager->newInstance()),
                'groups'=>$controllerInst->getSerializationGroups('cget'),
                ));
        }
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed $value
     */
    protected function setObjectProperty($object, $property, $value)
    {
        $refl = new \ReflectionProperty($object, $property);
        $refl->setAccessible(true);
        $refl->setValue($object, $value);
    }

}
