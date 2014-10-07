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

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController;

class OverriddenMethodController extends RadRestController
{
    public function getResourceManager()
    {
        return $this->container->get('resource_manager');
    }

    protected function getFormFactory()
    {
        return $this->container->get('form_factory');
    }

    public function getFormType()
    {
        return $this->container->get('form');
    }

    public function getAction($id)
    {
        return null;
    }
}
