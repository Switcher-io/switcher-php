# PHP Client for Switcher.io

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