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
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

/**
 * This trait provides routes for resource listing
 */
trait ListTrait
{
    use AbstractBaseTrait;

    /**
     * Returns a list of serializer groups for the given action on this controller
     *
     * @param string $action
     * @return string[] Serialization groups for this action
     */
    abstract protected function getSerializationGroups($action);

    /**
     * Paginates the page description for a page
     * @param PageDescriptionInterface $pageDescription
     * @param int $page
     * @return mixed
     */
    abstract protected function getPagination(PageDescriptionInterface $pageDescription, $page);

    /**
     * @ApiDoc(resource=true)
     * @AView
     */
    public function cgetAction(Request $request)
    {
        $list = $this->getFrontendManager()->getList(true);
        if(is_array($list)) {
            $view = View::create($list);
        } else {
            $view = View::create($this->getPagination($list, $request->query->get('page', 1)));
        }
        $view->getSerializationContext()->setGroups($this->getSerializationGroups('cget'));
        return $this->handleView($view);
    }
}
