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
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\ViewHandler\DefaultViewHandlerTrait;

class ViewHandlerController implements RadRestControllerInterface
{
    use DefaultViewHandlerTrait { handleView as public; }

    public function getSerializationGroups($action)
    {

    }

    public function getFormType()
    {

    }

    public function getResourceManager()
    {

    }

    public function getRouteName($action)
    {

    }
}
