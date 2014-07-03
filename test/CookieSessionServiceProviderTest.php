<?php

use Silex\Application;
use Renegare\SilexCSH\CookieSessionServiceProvider;
use Symfony\Component\HttpKernel\Client;

class CookieSessionServiceProviderTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        $app = new Application();

        $app->register(new CookieSessionServiceProvider);

        $app->get('/store-something', function(Application $app){
            $app['session']->set('hey', 'you');
            return 'Check your cookie!';
        });

        $app['exception_handler']->disable();
        $this->client = new Client($app, []);
    }

    /**
     * test the happy path
     */
    public function testCookieServiceSessionProvider() {
        $client = $this->client;

        $client->request('GET', '/store-something');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('Check your cookie!', $response->getContent());

        $cookie = $client->getCookieJar()->get('silexcsh');
        $this->assertEquals(['hey' => 'you'], $this->getSessionData($cookie));
    }

    public function getSessionData($cookie) {
        return unserialize(unserialize($cookie->getValue())[1]);
    }
}
