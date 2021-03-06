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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use vierbergenlars\Bundle\RadRestBundle\View\View;

/**
 * This trait provides routes for resource viewing
 */
trait ViewTrait
{
    use AbstractBaseTrait;

    /**
     * Returns a list of serializer groups for the given action on this controller
     *
     * @param string $action
     * @return string[] Serialization groups for this action
     */
    abstract public function getSerializationGroups($action);

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function getAction($id)
    {
        $object = $this->getResourceManager()->find($id);
        $view   = View::create($object);
        $view->getSerializationContext()->setGroups($this->getSerializationGroups('get'));

        return $this->handleView($view);
    }
}
