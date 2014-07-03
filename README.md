# SilexCSH

[Silex][1] Cookie Session Handler

[![Build Status](https://travis-ci.org/renegare/silexcsh.png?branch=master)](https://travis-ci.org/renegare/silexcsh)
[![Coverage Status](https://coveralls.io/repos/renegare/silexcsh/badge.png)](https://coveralls.io/r/renegare/silexcsh)

## Requirements

* PHP 5.4
* [composer][2] (preferably latest)

## Installation

```
$ composer require renegare/silexcsh:dev-master
```

## Usage Examples:

### Silex Usage
```
<?php

$app = new Silex\Application();

$app->register(new Renegare\SilexCSH\CookieSessionServiceProvider, [
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

```

## Test

Check out the repo and from the top level directory run the
following command (xdebug required for coverage):

```
$ composer update && vendor/bin/phpunit --coverage-text
```

## Credits

Inspired by: [nelmio/NelmioSecurityBundle][3]

[1]: http://silex.sensiolabs.org/doc/usage.html
[2]: https://getcomposer.org/download/
[3]: https://github.com/nelmio/NelmioSecurityBundle#cookie-session-handler
