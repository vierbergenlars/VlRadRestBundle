<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\EventListener;

use FOS\RestBundle\View\ViewHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use vierbergenlars\Bundle\RadRestBundle\View\View;

class ViewResponseListener implements EventSubscriberInterface
{
    const BASE_CONTROLLER_CLASS = 'vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface';

    private $viewHandler;
    private $templating;
    private $logger;

    public function __construct(ViewHandlerInterface $viewHandler, EngineInterface $templating, LoggerInterface $logger = null)
    {
        $this->viewHandler = $viewHandler;
        $this->templating  = $templating;
        $this->logger      = $logger;
    }

    /**
     * @codeCoverageIgnore
     */
    private function log($msg)
    {
        if($this->logger) {
            $this->logger->debug($msg);
        }
    }

    /**
     * Fallback to another template when the currently set/guessed template does not exist
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        // @codeCoverageIgnoreStart
        if(!is_array($controller)) {
            return;
        }
        if(!is_a($controller[0], self::BASE_CONTROLLER_CLASS)) {
            return;
        }
        // @codeCoverageIgnoreEnd

        $request  = $event->getRequest();
        $template = $request->attributes->get('_template');

        if($template instanceof TemplateReference) {
            // Search for alternative templates if the requested one does not exist
            if(!$this->templating->exists($template)) {
                $oldName = $template->getLogicalName();
                $map     = array('put'=>'edit', 'post'=>'new', 'delete'=>'remove');
                if(isset($map[$template->get('name')])) {
                    $template->set('name', $map[$template->get('name')]);
                    $this->log(sprintf('Template "%s" does not exist, trying alternative name "%s".', $oldName, $template->getLogicalName()));
                }
            }

            if(!$this->templating->exists($template)) {
                $oldName = $template->getLogicalName();
                $template->set('bundle', 'VlRadRestBundle');
                $template->set('controller', 'Default');
                $this->log(sprintf('Template "%s" does not exist, trying default template "%s".', $oldName, $template->getLogicalName()));
            }
        }
    }

    /**
     * Merge View extraData into View data when using a request format that uses templates
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        if($result instanceof View) {
            if($result->getFormat() === null) {
                $result->setFormat($event->getRequest()->getRequestFormat());
            }
            // If a templating format is in use, merge extradata variables into data
            if($this->viewHandler->isFormatTemplating($result->getFormat())) {
                $extraData = $result->getExtraData();
                $data      = $this->viewHandler->prepareTemplateParameters($result);
                $result->setData(array_merge($data, $extraData));
            }
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array('onKernelController', -12),
            KernelEvents::VIEW => array('onKernelView', 110),
        );
    }
}
