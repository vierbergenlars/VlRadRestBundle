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
use vierbergenlars\Bundle\RadRestBundle\ApiDoc\FrontendManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController;

class RadRestHandler implements HandlerInterface
{
    private $container;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $reflMethod)
    {
        $controller = $route->getDefault('_controller');
        
        $controllerPieces = explode('::', $controller);
        $controllerClass = $controllerPieces[0];
        $controllerMethod = $controllerPieces[1];
        
        $controllerInst = new $controllerClass();
        if(!($controllerInst instanceof RadRestController)) {
            return;
        }
        $controllerInst->setContainer($this->container);
        $frontendManager = $controllerInst->getFrontendManager();
        $serializationGroups = $controllerInst->getSerializationGroups();
        if(!isset($serializationGroups['object'])) {
            $serializationGroups['object'] = array('Default');
        }
        if(!isset($serializationGroups['list'])) {
            $serializationGroups['list'] = array('Default');
        }
        
        $resourceManager = $this->getObjectProperty($frontendManager, 'resourceManager');
        $formType = $this->getObjectProperty($frontendManager, 'formType');
        
        switch($controllerMethod) {
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
                    'groups'=>$serializationGroups['object'],
                ));
                break;
            case 'cgetAction':
                $this->setObjectProperty($annotation, 'output', array(
                    'class'=>get_class($resourceManager->create()),
                    'groups'=>$serializationGroups['list'],
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
