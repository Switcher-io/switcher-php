# PHP Client for Switcher.io

[![Build Status](https://travis-ci.org/switcher-io/switcher-php.svg?branch=master)](https://travis-ci.org/switcher-io/switcher-php)

## Installation

Using [composer](https://packagist.org/packages/switcher-io/switcher-php):

```bash
$ composer require switcher-io/switcher-php
```

## Dead Man Switch example

```php
$urlId = 'url identifier of the switch, e.g. "abc123" in https://dmsr.io/abc123.';
$key = 'switch key';

//initialize the api
$sw = new \SwitcherIO\DeadManSwitch($urlId, $key);

//call the /start endpoint to signal your job started (optional - only used if your switch has a max run time set)
$sw->start();

//your job code goes here

//call /complete to notifiy Switcher.io the job has finished
$sw->complete();

//you can also pause the switch
$sw->pause();
```

## Error handling

Exceptions will be thrown if an error occurs, or if Switcher.io does respond that the call is ok. The exception 
message will give detail on the issue.

```php
$sw = new \SwitcherIO\DeadManSwitch('url id', 'key');

try {
    $sw->complete();
} catch (\SwitcherIO\SwitcherException $e) {

    if ($e->getMessage() === 'Switch not found (404)') {
        //oops, you either got the url id or key wrong...
    }

}

```

## Dev environments

You probably don't want your local dev environment to ping a real switch. To make the library do dummy pings in
a local or dev environment, set the url id to 'test' or 'test-error':

```php
//in this case complete() will not actually ping a switcher.io url
$sw = new \SwitcherIO\DeadManSwitch('test', 'key-does-not-matter-for-test-url');
$sw->complete(); 

//in this case complete() will throw a \SwitcherIO\SwitcherException
$sw = new \SwitcherIO\DeadManSwitch('test-error', 'key-does-not-matter-for-test-url');
$sw->complete(); 
```