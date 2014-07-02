# SilexCSH ([Silex][1] Cookie Session Handler)

[![Build Status](https://travis-ci.org/renegare/sliexcsh.png?branch=master)](https://travis-ci.org/renegare/sliexcsh)
[![Coverage Status](https://coveralls.io/repos/renegare/sliexcsh/badge.png)](https://coveralls.io/r/renegare/sliexcsh)

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

echo "soon come\n";

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
