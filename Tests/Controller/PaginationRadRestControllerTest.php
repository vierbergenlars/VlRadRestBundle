<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\PageableUserRepository;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class PaginationRadRestControllerTest extends RadRestControllerTest
{
    protected function createFrontendManagerArgs() {
        $this->resourceManager = new PageableUserRepository();
        $this->resourceManager->fakeUsers = User::createArray(23);
        return parent::createFrontendManagerArgs();
    }

    public function testCGet()
    {
        $controller = $this->createController();

        $request = new Request();

        $retval = $controller->cgetAction($request);
        $this->assertSame(array_slice($this->resourceManager->fakeUsers, 0, 10), $retval->getData());
        $this->assertSame('data', $retval->getTemplateVar());
        $this->assertSame(200, $retval->getStatusCode());
        $this->assertSame(array('abc', 'def'), $retval->getSerializationContext()->attributes->get('groups')->get());

        $request->query->set('page', 2);
        $retval = $controller->cgetAction($request);
        $this->assertSame(array_slice($this->resourceManager->fakeUsers, 10, 10), $retval->getData());
    }
}
