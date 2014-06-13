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

use vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController;
use vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager;
use Symfony\Component\Routing\Router;
use Psr\Log\LoggerInterface;

class VoteServiceController extends ControllerServiceController
{
    public function __construct(Router $quux, $baz, FrontendManager $frontendManager, LoggerInterface $log)
    {
        parent::__construct($frontendManager, $log, $quux);
    }
}
