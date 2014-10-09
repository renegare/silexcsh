<?php

namespace Renegare\SilexCSH\Test;

use Renegare\SilexCSH\CookieSessionTestTrait;
use Silex\Application;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\BrowserKit\Cookie;

class CookieSessionTestTraitTest extends \PHPUnit_Framework_TestCase {
    use CookieSessionTestTrait;

    /**
     * @expectedException RuntimeException
     */
    public function testGetCookieSessionNameUnconfiguredAppException() {
        $this->getCookieSessionName(new Application);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetCookieSessionNameIncorrectTypeException() {
        $this->getCookieSessionName(1);
    }

    public function provideIncorrectValues() {
        return [
            ['incorrect'],
            [serialize('incorrect')],
            [serialize([serialize(['incorrect'])])],
            [serialize([0, 'incorrect'])],
            [null]
        ];
    }
    /**
     * @dataProvider provideIncorrectValues
     * @expectedException RuntimeException
     */
    public function testGetSessionDataIncorrectSessionData($value) {
        $cookie = new Cookie('TEST', $value);
        $client = new Client(new Application);
        $client->getCookieJar()->set($cookie);

        $this->getSessionData($client, 'TEST');
    }
}
