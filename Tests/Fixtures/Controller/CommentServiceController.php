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
use Psr\Log\LoggerInterface;

class CommentServiceController extends ControllerServiceController
{
    public function __construct(\stdClass $bar, LoggerInterface $quux, FrontendManager $frontendManager, $baz)
    {
        parent::__construct($frontendManager, $quux);
    }
}
