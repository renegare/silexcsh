<?php

use Renegare\SilexCSH\CookieSessionHandler;

class CookieSessionHandlerTest extends \PHPUnit_Framework_TestCase {

    public function testSessionHandler() {
        $handler = new CookieSessionHandler('test_session');
        $this->assertInstanceOf('SessionHandlerInterface', $handler);
    }

    public function testCloseAlwaysReturnTrue() {
        $handler = new CookieSessionHandler('test_session');
        $this->assertTrue($handler->close());
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

        $this->assertEquals($sessionData, $handler->read(''));
    }

    public function testReadMethodNoSession() {
        $handler = new CookieSessionHandler('test_session');

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $mockCookieJar = new Symfony\Component\HttpFoundation\ParameterBag;

        $request->cookies = $mockCookieJar;
        $handler->setRequest($request);
        $this->assertEquals('', $handler->read(''));
    }

    public function provideInvalidPayloads() {
        return [
            [serialize(['session' => 'data'])],
            [serialize(['session' => 'data', 'hmmm' => 'dssddsd'])],
            [serialize(['data', 'dssddsd'])],
            [serialize('12312,jkskjsdkjds')],
            ['12312,jkskjsdkjds'],
            [null],
            [serialize([strtotime('-1 hour'), '{serialized.data}'])]
        ];
    }

    /**
     * @dataProvider provideInvalidPayloads
     */
    public function testReadMethodInvalidSessionData($invalidPayload) {
        $handler = new CookieSessionHandler('test_session');

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $mockCookieJar = new Symfony\Component\HttpFoundation\ParameterBag;
        $mockCookieJar->set('test_session', $invalidPayload);
        $request->cookies = $mockCookieJar;
        $handler->setRequest($request);

        $this->assertEquals('', $handler->read(''));
    }

    public function testWriteMethod() {
        $handler = new CookieSessionHandler('test_session');

        $sessionData = '{serialized_session_data}';
        $handler->write('', $sessionData);

        $cookie = $handler->getCookie();
        $this->assertEquals('test_session', $cookie->getName());
        $this->assertEquals(serialize([0,$sessionData]), $cookie->getValue());
        $this->assertNull($cookie->getDomain());
        $this->assertEquals(0, $cookie->getExpiresTime());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals(false, $cookie->isSecure());
        $this->assertEquals(true, $cookie->isHttpOnly());
    }

    public function testGCMethod() {
        $handler = new CookieSessionHandler('test_session');
        $this->assertTrue($handler->gc(''));
    }

    public function testDestroyMethod() {
        $handler = new CookieSessionHandler('test_session');
        $this->assertFalse($handler->getCookie());
        $this->assertTrue($handler->destroy(''));
        $this->assertNull($handler->getCookie());
    }

    public function testGetCookieName() {
        $handler = new CookieSessionHandler('test_session');        
        $this->assertEquals('test_session', $handler->getCookieName());
    }
}
