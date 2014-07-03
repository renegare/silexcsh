<?php

namespace Renegare\SilexCSH;

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\HttpFoundation\Cookie;

class CookieSessionServiceProvider extends SessionServiceProvider {

    protected $app;

    public function register(Application $app)
    {
        parent::register($app);

        $this->app = $app;

        $app['session.storage.handler'] = $app->share(function ($app) {
            return new CookieSessionHandler('silexcsh');
        });

        $app['session.storage.native'] = $app->share(function ($app) {
            return new PhpBridgeSessionStorage(
                $app['session.storage.handler']
            );
        });
    }

    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        parent::onEarlyKernelRequest($event);

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        // bootstrap the session
        if (!isset($this->app['session'])) {
            return;
        }

        $request = $event->getRequest();
        $this->app['session.storage.handler']->setRequest($request);

        /*
        $session = $this->app['session'];
        var_dump($session->getName());
        die;
        $cookies = $event->getRequest()->cookies;

        if ($cookies->has($session->getName())) {
            $session->setId($cookies->get($session->getName()));
        } else {
            $session->migrate(false);
        }

        $session = $this->app['session'];
        */

    }

    public function onKernelResponse(FilterResponseEvent $event) {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $session = $event->getRequest()->getSession();

        if ($session && $session->isStarted()) {
            $session->save();

            $handler = $this->app['session.storage.handler'];
            $handler->write('', serialize($session->all()));
            $cookie = $handler->getCookie();

            if($cookie instanceof Cookie) {
                $event->getResponse()
                    ->headers->setCookie($cookie);
            }
        }
    }

    public function boot(Application $app) {
        $app['dispatcher']->addListener(KernelEvents::REQUEST, array($this, 'onEarlyKernelRequest'), 128);

        // $app['dispatcher']->addListener(KernelEvents::REQUEST, array($this, 'onKernelRequest'), 192);
        $app['dispatcher']->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'), -128);
    }
}
