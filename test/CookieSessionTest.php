<?php

use Renegare\SilexCSH\CookieSession;

class CookieSessionTest extends \PHPUnit_Framework_TestCase {

    public function testCookieSessionMigrationOnlyReturnsTrue() {
        $session = new CookieSession();
        $this->assertTrue($session->migrate());
    }
}
