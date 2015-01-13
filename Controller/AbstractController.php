<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View as AView;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;
use vierbergenlars\Bundle\RadRestBundle\Twig\ControllerVariables;
use vierbergenlars\Bundle\RadRestBundle\View\View;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\DefaultsTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\CreateTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\DeleteTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\EditTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ListTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\PatchTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ViewTrait;

abstract class AbstractController implements RadRestControllerInterface
{
    use DefaultsTrait;
    use CreateTrait;
    use DeleteTrait;
    use EditTrait;
    use ListTrait;
    use PatchTrait;
    use ViewTrait;
}
