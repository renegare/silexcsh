<?php

use Silex\Application;
use Renegare\SilexCSH\CookieSessionServiceProvider;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\BrowserKit\Cookie;

class CookieSessionServiceProviderTest extends \PHPUnit_Framework_TestCase {

    protected $client;
    protected $app;
    protected $cookieName = 'CUSTOMNAME';

    public function setUp() {
        $app = new Application();

        $app->register(new CookieSessionServiceProvider, [
            'session.cookie.options' => [
                'name' => 'CUSTOMNAME', // string
                'lifetime' => 0,        // int
                'path' => '/',          // string
                'domain' => null,       // string
                'secure' => false,      // boolean
                'httponly' => true      // boolean
            ]
        ]);

        $app->get('/doing-nothing', function(Application $app) {
            return 'Nothing going on here with sessions';
        });

        $app->get('/persist', function(Application $app){
            $app['session']->set('message', 'Hello There!');
            return 'Check your cookie!';
        });

        $app->get('/read', function(Application $app){
            return print_r($app['session']->all(), true);
        });

        $app->get('/destroy', function(Application $app) {
            $app['session']->clear();
            return 'Ok Bye Bye!';
        });

        // test setup code so any errors is made visible
        $app['exception_handler']->disable();
        $this->client = new Client($app, []);
        $this->app = $app;
    }

    /**
     * test nothing going on here
     */
    public function testNoCookieIsSetIfNoAccessIsMadeToTheSession() {
        $client = $this->client;

        $client->request('GET', '/doing-nothing');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('Nothing going on here with sessions', $response->getContent());

        $cookie = $client->getCookieJar()->get($this->cookieName);
        $this->assertNull($cookie);
    }
    /**
     * test the persistence
     */
    public function testSessionIsPersistedToCookie() {
        $client = $this->client;

        $client->request('GET', '/persist');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('Check your cookie!', $response->getContent());

        $cookie = $client->getCookieJar()->get($this->cookieName);
        $this->assertNotNull($cookie);
        $this->assertEquals(['message' => 'Hello There!'], $this->getSessionData($cookie));
    }

    /**
     * test the read
     */
    public function testSessionIsReadFromCookie() {
        $client = $this->client;
        $sessionData = ['message' => time()];

        $client->getCookieJar()->set($this->createSessionCookie($sessionData));

        $client->request('GET', '/read');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals(print_r($sessionData, true), $response->getContent());
    }

    /**
     * test delete
     */
    public function testCookieIsDeletedWhenSessionIsCleared() {
        $client = $this->client;

        $client->request('GET', '/persist');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('Check your cookie!', $response->getContent());

        $cookie = $client->getCookieJar()->get($this->cookieName);
        $this->assertNotNull($cookie);
        $cookieData = $this->getSessionData($cookie);
        $this->assertEquals(['message' => 'Hello There!'], $cookieData);

        $client->request('GET', '/read');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals(print_r($cookieData, true), $response->getContent());

        $client->request('GET', '/destroy');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('Ok Bye Bye!', $response->getContent());
        $this->assertNull($client->getCookieJar()->get($this->cookieName));

        $client->request('GET', '/read');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals(print_r([], true), $response->getContent());
        $this->assertNull($client->getCookieJar()->get($this->cookieName));
    }

    public function testSessionNameMatchesCookieName() {
        $client = $this->client;
        $app = $this->app;

        $client->request('GET', '/persist');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        $session = $app['session'];
        $sessionOptions = $app['session.cookie.options'];

        $this->assertInstanceOf('Renegare\SilexCSH\CookieSession', $session);
        $this->assertEquals($sessionOptions['name'], $session->getName());
    }

    protected function getSessionData(Cookie $cookie) {
        return unserialize(unserialize($cookie->getValue())[1]);
    }

    protected function createSessionCookie(array $data) {
        $cookie = new Cookie($this->cookieName, serialize([0, serialize($data)]));
        return $cookie;
    }
}
