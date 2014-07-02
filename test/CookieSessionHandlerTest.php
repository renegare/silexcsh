<?php

use Renegare\SilexCSH\CookieSessionHandler;

class CookieSessionHandlerTest extends \PHPUnit_Framework_TestCase {

    public function testSessionHandler() {
        $handler = new CookieSessionHandler();
        $this->assertInstanceOf('SessionHandlerInterface', $handler);
    }

    public function testCloseAlwaysReturnTrue() {
        $handler = new CookieSessionHandler();
        $this->assertTrue($handler->close());
    }

    public function testDestroyMethod() {
        $this->markIncomplete();
    }

    public function testGCMethod() {
        $this->markIncomplete();
    }

    public function testOpenMethod() {
        $handler = new CookieSessionHandler('test_session');

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $mockCookieJar = new Symfony\Component\HttpFoundation\ParameterBag;
        $mockCookieJar->set('test_session', '...');
        $request->cookies = $mockCookieJar;
        $handler->setRequest($request);

        $this->assertTrue($handler->open('', ''));

        $mockCookieJar->remove('test_session');
        $this->assertFalse($handler->open('', ''));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testOpenMethodException() {
        $handler = new CookieSessionHandler('test_session');
        $this->assertTrue($handler->open('', ''));
    }

    public function testReadMethod() {
        $handler = new CookieSessionHandler('test_session');

        $sessionData = serialize(['session' => 'data']);

        $payload = serialize([strtotime('1 hour'), $sessionData]);

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $mockCookieJar = new Symfony\Component\HttpFoundation\ParameterBag;
        $mockCookieJar->set('test_session', $payload);
        $request->cookies = $mockCookieJar;
        $handler->setRequest($request);

        $this->assertTrue($handler->open('', ''));

        $this->assertEquals($sessionData, $handler->read(''));
    }
}
