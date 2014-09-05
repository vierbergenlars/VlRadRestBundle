<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ViewTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ListTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\CreateTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\EditTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\PatchTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\DeleteTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\DefaultsTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultClassRoutingTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TraitedController implements RadRestControllerInterface
{
    use ListTrait;
    use ViewTrait;
    use CreateTrait;
    use EditTrait;
    use PatchTrait;
    use DeleteTrait;
    use DefaultsTrait { DefaultsTrait::getSerializationGroups as private _parent_getSerializationGroups; }
    use DefaultClassRoutingTrait;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFrontendManager()
    {
        return $this->container->get('frontend_manager');
    }

    protected function getLogger()
    {
        return null;
    }

    protected function getRouter()
    {
        return $this->container->get('router');
    }

    public function getSerializationGroups($action)
    {
        if($action === 'cget') {
            return array('abc', 'def');
        }
        return $this->_parent_getSerializationGroups($action);
    }
}
