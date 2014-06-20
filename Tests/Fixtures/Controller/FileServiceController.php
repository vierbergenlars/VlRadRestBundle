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

class FileServiceController extends ControllerServiceController
{
    public function __construct(FrontendManager $frontendManager)
    {
        parent::__construct($frontendManager);
    }
}
