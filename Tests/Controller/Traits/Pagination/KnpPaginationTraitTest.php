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
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination\KnpPaginationTrait
 */
class KnpPaginationTraitTest extends \PHPUnit_Framework_TestCase
{
    private $paginationTrait;
    private $knpPaginator;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        if(!class_exists('Knp\Component\Pager\Paginator'))
            return $this->markTestSkipped('Knp Paginator bundle is not installed');

        if(!trait_exists(__NAMESPACE__.'\\_KnpPaginationTrait', false)) {
            // Expose KnpPaginationTrait::getPagination as public
            eval(
                'namespace '.__NAMESPACE__.';
                use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination\KnpPaginationTrait;
                trait _KnpPaginationTrait {
                    use KnpPaginationTrait { getPagination as public;}
                }'
            );
        }

        $this->knpPaginator = $this->getMock('Knp\Component\Pager\Paginator');

        $this->paginationTrait = $this->getMockBuilder(__NAMESPACE__.'\_KnpPaginationTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->paginationTrait->expects($this->once())
            ->method('getPaginator')
            ->with()
            ->willReturn($this->knpPaginator);
    }

    public function testGetPagination()
    {
        $pageDescription = new ArrayPageDescription($users = User::createArray(25));

        $this->knpPaginator->expects($this->once())
            ->method('paginate')
            ->with($pageDescription, 1)
            ->willReturn($pagination = $this->getMock('\Knp\Component\Pager\Pagination\PaginationInterface'));

        $this->assertSame($pagination, $this->paginationTrait->getPagination($pageDescription, 1));
    }
}
