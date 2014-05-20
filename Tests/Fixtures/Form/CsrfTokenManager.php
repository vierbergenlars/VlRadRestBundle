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

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class CsrfTokenManager implements CsrfTokenManagerInterface
{
    public function getToken($id)
    {
        var_dump($id);
        return new CsrfToken($id, 'abcd');
    }
    
    public function refreshToken($tokenId)
    {
        
    }
    
    public function removeToken($tokenId)
    {
        
    }
    
    public function isTokenValid(CsrfToken $token)
    {
        return $token->getValue() === 'abcd';
    }
}