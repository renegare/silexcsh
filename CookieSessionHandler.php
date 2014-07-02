<?php

namespace Renegare\SilexCSH;

use Symfony\Component\HttpFoundation\Request;

class CookieSessionHandler implements \SessionHandlerInterface {

    /** @var Request */
    protected $request;

    /** @var string */
    protected $name;

    /**
     * @param string $cookieName - name of the cookie that will used to store the session
     */
    public function __construct($cookieName) {
        $this->name = $cookieName;
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
    public function destroy($session_id) {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime) {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
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
    public function read( $session_id) {
        $cookies = $this->getRequest()->cookies;

        if(!$cookies->has($this->name)) {
            return '';
        }

        $content = @unserialize($cookies->get($this->name));

        if ($content === false || count($content) !== 2) {
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
    public function write($session_id, $session_data) {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
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
}
