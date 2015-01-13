<?php

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Security;

use vierbergenlars\Bundle\RadRestBundle\Security\AllowAllAuthorizationChecker;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Security\AllowAllAuthorizationChecker
 */
class AllowAllAuthorizationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $authorizationChecker = new AllowAllAuthorizationChecker();
        $this->assertTrue($authorizationChecker->mayList());
        $this->assertTrue($authorizationChecker->mayCreate(new \stdClass()));
        $this->assertTrue($authorizationChecker->mayView(new \stdClass()));
        $this->assertTrue($authorizationChecker->mayEdit(new \stdClass()));
        $this->assertTrue($authorizationChecker->mayDelete(new \stdClass()));
    }
}
