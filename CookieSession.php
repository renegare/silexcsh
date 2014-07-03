<?php

namespace Renegare\SilexCSH;

use Symfony\Component\HttpFoundation\Session\Session;

class CookieSession extends Session {

    /**
     * {@inheritdoc}
     */
    public function migrate($destroy = false, $lifetime = null)
    {
        return true;
    }
}
