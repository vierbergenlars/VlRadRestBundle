<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Form;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

class CsrfProvider implements CsrfProviderInterface
{
    public function generateCsrfToken($intention)
    {
        return 'abcd';
    }

    public function isCsrfTokenValid($intention, $token)
    {
        return $token === 'abcd';
    }
}
