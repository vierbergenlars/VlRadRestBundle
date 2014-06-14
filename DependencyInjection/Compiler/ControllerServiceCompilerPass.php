<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ControllerServiceCompilerPass implements CompilerPassInterface
{
    const BASE_CONTROLLER_CLASS  = 'vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController';
    const FRONTEND_MANAGER_CLASS = 'vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager';
    const LOGGER_INTERFACE       = 'Psr\Log\LoggerInterface';
    const LOGGER_SERVICE         = 'logger';
    const ROUTER_CLASS           = 'Symfony\Component\Routing\Router';
    const ROUTER_SERVICE         = 'router';
    const ROUTE_NAME_METHOD      = 'getRouteName';

    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('radrest.controller');
        $helper     = new Helpers($container);
        foreach($serviceIds as $serviceId => $tagAttributes)
        {
            foreach($tagAttributes as $attribute)
            {
                // @codeCoverageIgnoreStart
                if(!isset($attribute['resource'])) {
                    throw new \LogicException('radrest.controller tag requires attribute resource. (service: '.$serviceId.')');
                }
                // @codeCoverageIgnoreEnd

                $serviceDefinition = $container->findDefinition($serviceId);
                $frontendManagerId = $helper->findTaggedServiceIdByAttributes('radrest.frontend_manager', array('resource'=>$attribute['resource']));

                if($this->hasDefaultConstructor($serviceDefinition)) {
                    $this->processDefaultConstructor($serviceDefinition, $serviceId, $frontendManagerId);
                } else {
                    $this->processService($serviceDefinition, $frontendManagerId);
                }
            }
        }
    }

    protected function hasDefaultConstructor(Definition $definition)
    {
        $reflectionClass = new \ReflectionClass($definition->getClass());
        $constructor     = $reflectionClass->getConstructor();
        $declaringClass  = $constructor->getDeclaringClass();
        return $declaringClass->name === self::BASE_CONTROLLER_CLASS;
    }

    protected function processDefaultConstructor(Definition $definition, $serviceId, $frontendManagerId)
    {
        $reflectionClass  = new \ReflectionClass($definition->getClass());
        $redirectToMethod = $reflectionClass->getMethod(static::ROUTE_NAME_METHOD);
        if($redirectToMethod->getDeclaringClass()->name === self::BASE_CONTROLLER_CLASS) {
            $definition->setArguments(array(
                new Reference($frontendManagerId),
                new Reference(static::LOGGER_SERVICE),
                new Reference(static::ROUTER_SERVICE),
                $serviceId
            ));
        } else {
            $definition->setArguments(array(
                new Reference($frontendManagerId),
                new Reference(static::LOGGER_SERVICE)
            ));
        }
    }

    protected function processService(Definition $definition, $frontendManagerId)
    {
        $reflectionClass = new \ReflectionClass($definition->getClass());
        $constructor     = $reflectionClass->getConstructor();
        $parameters      = $constructor->getParameters();
        $arguments       = $definition->getArguments();

        foreach($parameters as $parameter)
        {
            $typeHint = $parameter->getClass();
            if($typeHint !== null) {
                switch($typeHint->name) {
                    case static::FRONTEND_MANAGER_CLASS:
                        array_splice($arguments, $parameter->getPosition(), 0, array(new Reference($frontendManagerId)));
                        break;
                    case static::LOGGER_INTERFACE:
                        array_splice($arguments, $parameter->getPosition(), 0, array(new Reference(static::LOGGER_SERVICE)));
                        break;
                    case static::ROUTER_CLASS:
                        array_splice($arguments, $parameter->getPosition(), 0, array(new Reference(static::ROUTER_SERVICE)));
                }
            }
        }

        $definition->setArguments($arguments);
    }
}
