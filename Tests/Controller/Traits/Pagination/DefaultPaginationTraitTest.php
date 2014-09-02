<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Pagination;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Pagination\ArrayPageDescription;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination\DefaultPaginationTrait
 */
class DefaultPaginationTraitTest extends \PHPUnit_Framework_TestCase
{
    private $paginationTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        if(!trait_exists(__NAMESPACE__.'\\_DefaultPaginationTrait', false)) {
            // Expose DefaultPaginationTrait::getPagination as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination\DefaultPaginationTrait;
                trait _DefaultPaginationTrait {
                    use DefaultPaginationTrait { getPagination as public;}
                }'
            );
        }

        $this->paginationTrait = $this->getMockBuilder(__NAMESPACE__.'\_DefaultPaginationTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();
    }

    public function testGetPagination()
    {
        $pageDescription = new ArrayPageDescription($users = User::createArray(25));
        $this->assertEquals(array_slice($users, 0, 10), $this->paginationTrait->getPagination($pageDescription, 1));
        $this->assertEquals(array_slice($users, 10, 10), $this->paginationTrait->getPagination($pageDescription, 2));
    }
}
