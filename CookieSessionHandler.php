<?php

namespace Renegare\SilexCSH;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

class CookieSessionHandler implements \SessionHandlerInterface {

    /** @var Request */
    protected $request;

    /** @var string */
    protected $name;
    protected $path;
    protected $domain;

    /** @var int */
    protected $lifeTime;

    /** @var bool */
    protected $secure;
    protected $httpOnly;

    /** 
     * @var mixed(false|null|Cookie) - effects getCookie return value
     * > false - means no cookie was accessed|written too and therefore no cookie will be returned
     * > null - means cookie was destroyed
     * > Cookie - means some data has been written and a cookie will be returned
     */ 
    protected $cookie = false;

    /**
     * @param string $cookieName - name of the cookie that will used to store the session
     * @param int             $lifeTime
     * @param string          $path
     * @param string          $domain
     * @param bool            $secure
     * @param bool            $httpOnly
     */
    public function __construct($cookieName, $lifeTime = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true) {
        $this->name = $cookieName;
        $this->lifeTime = $lifeTime;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId) {
        $this->cookie = null;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifetime) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId) {
        return $this->getRequest()
            ->cookies->has($this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId) {
        $cookies = $this->getRequest()->cookies;

        $content = @unserialize($cookies->get($this->name, ''));

        if ($content === false || count($content) !== 2 ||
            !isset($content[0]) || !isset($content[1])) {
            $content = [time(), ''];
        }

        list($expire, $data) = $content;

        if($expire !== 0 && $expire < strtotime('now')) {
            return '';
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData) {

        $expire = $this->lifeTime === 0 ? 0 : strtotime('now') + $this->lifeTime;

        $this->cookie = new Cookie(
            $this->name,
            serialize([$expire, $sessionData]),
            $expire,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );

        return true;
    }

    /**
     * set request that holds the current cookie session
     * @param Request $request
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * get request that holds the current cookie session
     * @throws RuntimeException
     * @return Request
     */
    protected function getRequest() {
        if(!$this->request) {
            throw new \RuntimeException('You cannot access the session without a Request object set');
        }
        return $this->request;
    }

    /**
     * get written cookie or a delete cookie if no cookie has been written
     * @return mixed false|null|Cookie
     */
    public function getCookie() {
        return $this->cookie;
    }

    /**
     * get cookie name
     * @return string
     */
    public function getCookieName() {
        return $this->name;
    }
}
