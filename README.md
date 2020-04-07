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

# Error handling

A `\SwitcherIO\SwitcherException` will be thrown if an error occurs, or if Switcher.io does respond that the call is ok. `SwitcherException` has a `getStatusCode` method that you should use to determine why an exception was thrown. The status
code will match a constant of `\SwitcherIO\DeadManSwitch`.

**Make sure to use error handling!** If you don't, a call to `$sw->start()` could prevent your job from finishing.

```php
use \SwitcherIO\DeadManSwitch;

$sw = new DeadManSwitch('url id', 'key');

try {
    $sw->start();
} catch (\SwitcherIO\SwitcherException $e) {

    if ($e->getStatusCode() === DeadManSwitch::STATUS_ERROR_404) {
        //oops, you either got the url id or key wrong...
    } else if ($e->getStatusCode() === DeadManSwitch::STATUS_ERROR_START_BEFORE_COMPLETE) {
        /*
         * You get this error if your switch is using a max runtime, and for some reason your job
         * starts a new run before the last run finished. If this is a problem for you, handle it here...
         */
    }

}

```

## Dev environments

You probably don't want your local dev environment to ping a real switch. To make the library do dummy pings in
a local or dev environment, set the url id to 'test' or 'test-error':

```php
//in this case complete() act as if it ran succesfully, and will not actually ping a switcher.io url
$sw = new \SwitcherIO\DeadManSwitch('test', 'key-does-not-matter-for-test-url');
$sw->complete(); 

/*
 * In this case complete() will throw a \SwitcherIO\SwitcherException with a status code 
 * of \SwitcherIO\DeadManSwitch::STATUS_TEST_ERROR
 */
$sw = new \SwitcherIO\DeadManSwitch('test-error', 'key-does-not-matter-for-test-url');
$sw->complete(); 
```