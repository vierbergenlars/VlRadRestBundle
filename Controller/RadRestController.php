<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultClassRoutingTrait;

/**
 * Base Controller for Controllers using the RAD Rest functionality
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
abstract class RadRestController extends AbstractController implements ContainerAwareInterface
{
    use DefaultClassRoutingTrait;

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function getLogger()
    {
        return $this->container->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    protected function getRouter()
    {
        return $this->container->get('router', ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    protected function getFormFactory()
    {
        return $this->container->get('form_factory');
    }
}
