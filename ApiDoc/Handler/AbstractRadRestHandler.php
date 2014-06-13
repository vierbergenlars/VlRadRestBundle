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

use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Route;
use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface;

abstract class AbstractRadRestHandler implements HandlerInterface
{
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
        if($reflMethod->getDeclaringClass()->getName() !== 'vierbergenlars\Bundle\RadRestBundle\Controller\AbstractController') {
            return;
        }

        $controllerInst = $this->getControllerInstance($route);

        $frontendManager     = $controllerInst->getFrontendManager();

        $resourceManager = $this->getObjectProperty($frontendManager, 'resourceManager');
        $formType        = $this->getObjectProperty($frontendManager, 'formType');

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
                'class'=>get_class($resourceManager->create()),
                'groups'=>$controllerInst->getSerializationGroups('get')?:array('Default'),
                ));
                break;
            case 'cgetAction':
                $this->setObjectProperty($annotation, 'output', array(
                'class'=>get_class($resourceManager->create()),
                'groups'=>$controllerInst->getSerializationGroups('cget')?:array('Default'),
                ));
        }
    }

    private function setObjectProperty($object, $property, $value)
    {
        $refl = new \ReflectionProperty($object, $property);
        $refl->setAccessible(true);
        $refl->setValue($object, $value);
    }

    private function getObjectProperty($object, $property)
    {
        $refl = new \ReflectionProperty($object, $property);
        $refl->setAccessible(true);
        return $refl->getValue($object);
    }
}
