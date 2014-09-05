<?php

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Router;
use Knp\Component\Pager\Paginator;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ViewTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ListTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\BaseTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\CreateTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\EditTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\DeleteTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultServiceRoutingTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination\KnpPaginationTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Redirect\DefaultRedirectTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\DefaultsTrait;

class TraitedController implements RadRestControllerInterface, ClassResourceInterface
{
    use ViewTrait;
    use ListTrait;
    use CreateTrait;
    use EditTrait;
    use DeleteTrait;
    // This controller will be registered as a service, so we need the service routing trait
    use DefaultServiceRoutingTrait;
    // Knp pagination because it looks nicer
    use KnpPaginationTrait;
    use DefaultsTrait {
        // Use Knp pagination instead of the default pagination implementation
        KnpPaginationTrait::getPagination insteadof DefaultsTrait;
    }

    private $frontendManager;

    private $logger;

    private $router;

    private $paginator;

    public function __construct(FrontendManager $frontendManager, LoggerInterface $logger, Router $router, Paginator $paginator)
    {
        $this->frontendManager = $frontendManager;
        $this->logger          = $logger;
        $this->router          = $router;
        $this->paginator       = $paginator;
    }

    /**
     * Required by ViewTrait, ListTrait, CreateTrait, EditTrait, DeleteTrait
     */
    public function getFrontendManager()
    {
        return $this->frontendManager;
    }

    /**
     * Required by DefaultServiceRoutingTrait
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Required by DefaultServiceRoutingTrait
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * Required by DefaultServiceRoutingTrait
     */
    protected function getServiceName()
    {
        return 'radrest.example.traited_controller';
    }

    /**
     * Required by KnpPaginationTrait
     */
    protected function getPaginator()
    {
        return $this->paginator;
    }
}
