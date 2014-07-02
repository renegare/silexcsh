<?php

use Renegare\SilexCSH\CookieSessionHandler;

class CookieSessionHandlerTest extends \PHPUnit_Framework_TestCase {

    public function testSessionHandler() {
        $cookieHandler = new CookieSessionHandler();
        $this->assertInstanceOf('SessionHandlerInterface', $cookieHandler);
    }
}
