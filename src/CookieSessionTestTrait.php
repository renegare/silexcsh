<?php

namespace Renegare\SilexCSH;

use Silex\Application;
use Symfony\Component\HttpKernel\Client;

trait CookieSessionTestTrait {

    /**
     * get session data from cookie stored on the client side
     * @param Client $client - client containing the cookie data
     * @param Application|string $app - if Application we retrieve the cookie name from the 'session.cookie.options' configuration else we assume this is the name of the cookie
     * @throws RuntimeException - when something is amiss, please read the exception message
     * @return array
     */
    public function getSessionData(Client $client, $app) {
        $data = [];
        $cookieName = $this->getCookieSessionName($app);
        $jar = $client->getCookieJar();
        $cookie = $client->getCookieJar()->get($cookieName);
        if($cookie) {
            $rawData = $cookie->getValue();
            $value = @unserialize($rawData);
            if(!is_array($value)|| count($value) !== 2) {
                throw new \RuntimeException('Bad cookie data: ' . $rawData);
            }

            $data = @unserialize($value[1]);
            if(!is_array($data)) {
                throw new \RuntimeException('Bad session data: ' . print_r($value[1], true));
            }
        }

        return $data;
    }

    /**
     * get session data from cookie stored on the client side
     * @param Application|string $app - if Application we retrieve the cookie name from the 'session.cookie.options' configuration else we assume this is the name of the cookie
     * @throws RuntimeException - when passed application has not registered CookieSessionServiceProvider
     * @return string
     */
    public function getCookieSessionName($app) {
        if($app instanceOf Application) {
            if(isset($app['session.cookie.options'])) {
                $options = CookieSessionServiceProvider::mergeDefaultOptions(isset($app['session.cookie.options'])? $app['session.cookie.options'] : []);
                $app = $options['name'];
            } else {
                throw new \RuntimeException('You need to register Renegare\SilexCSH\CookieSessionServiceProvider in order to determine the correct cookie session name.');
            }
        }

        if(!is_string($app)) {
            throw new \RuntimeException('$app must be either be an instance of Silex\Application or a string.');
        }

        return $app;
    }
}
