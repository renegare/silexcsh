<?php

namespace Renegare\SilexCSH;

class CookieSessionHandler implements \SessionHandlerInterface {

    /**
     * {@inheritdoc}
     */
    public function close() {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
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
    public function open($save_path , $name) {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
    }

    /**
     * {@inheritdoc}
     */
    public function read( $session_id) {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
    }

    /**
     * {@inheritdoc}
     */
    public function write($session_id, $session_data) {
        throw new \Exception(sprintf('Not Implemented: %s::%s', __CLASS__, __METHOD__));
    }
}
