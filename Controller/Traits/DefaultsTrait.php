<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits;

use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Pagination\DefaultPaginationTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Redirect\DefaultRedirectTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Serialization\DefaultSerializationGroupsTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\ViewHandler\DefaultViewHandlerTrait;
use vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Form\DefaultFormTrait;

/**
 * This trait groups
 */
trait DefaultsTrait
{
    use DefaultPaginationTrait;
    use DefaultRedirectTrait;
    use DefaultSerializationGroupsTrait;
    use DefaultViewHandlerTrait;
    use DefaultFormTrait;
}
