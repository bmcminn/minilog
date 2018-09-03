# Minilog

A dependency free PHP logging utility that just friggin' works :)


## Why yet another logging utility?

I like projects like Monolog, but they lack basic conveniences I like about the native `console.log()` in JavaScript; plus I have a niceities baked into Minilog that you may like as well:

- dependency free
- Each log method accepts N arguments and automagically concatenates them into a log message (just like JS console.log!!! :D)
- automagically JSON encodes arrays and objects (just like JS console.log :P), and tags the output accordingly
- automagically determines which CLI stream to write to; allowing for CLI logging when running on PHP's internal server
- automagically applies color coding of CLI messages based on log level severity
- conditionally allows each logger instance to log the calling file and line number so you know where the log message originated from
- automagically prepends each log file with the Y-M-D for daily log rotation


## Installation
Open up your favorite CLI and enter the typical `composer require` command:

```sh
> composer require gbox/minilog
```

BOOM! Now get logging!


## Usage:

Using `Minilog` is pretty straightforward. You define a `new \Gbox\Minilog()` class intance, you pass in the name of the logger context, and you may pass an option associative array of options to configure it for your needs.

```php
<?php

require "vendor/autoload.php";

//                  Minilog($logname [, $options[] ])
$Logger = new \Gbox\Minilog('logName', [
    // write log entries to the console
    'console'    => true,            // bool   : defaults true

    // defines where log files should be written to
    'dir'        => './logs',        // string : defaults '.'

    // defines the minimum RFC 5424 level to log
    'level'      => 'DEBUG',         // string : defaults 'DEBUG'

    // defines whether to log the path and line number of the log call
    'linenos'    => true,            // bool   : defaults true

    // defines the timestamp format
    'timestamp'  => '[Y-m-d H:m:s]', // string : you can change this if you want
]);


$Logger->debug('testing', 'message', 'here');     // testing message here
$Logger->info('testing', 'message', 'here');      // testing message here
$Logger->notice('testing', 'message', 'here');    // testing message here
$Logger->warning('testing', 'message', 'here');   // testing message here
$Logger->error('testing', 'message', 'here');     // testing message here
$Logger->critical('testing', 'message', 'here');  // testing message here
$Logger->alert('testing', 'message', 'here');     // testing message here
$Logger->emergency('testing', 'message', 'here'); // testing message here
```


## Example Output:

You can see a sample of Minilog in action by cloning this repo and running the following commands in your terminal of choice:

```sh
> php test-minilog.php
[2018-07-23 08:07:36] testing_logs.DEBUG:     test-minilog.php:15 testing debug messages (BOOL) true
[2018-07-23 08:07:36] testing_logs.INFO:      test-minilog.php:16 testing info messages (BOOL) true
[2018-07-23 08:07:36] testing_logs.NOTICE:    test-minilog.php:17 testing notice messages (BOOL) true
[2018-07-23 08:07:36] testing_logs.WARNING:   test-minilog.php:18 testing warning messages (BOOL) false
[2018-07-23 08:07:36] testing_logs.ERROR:     test-minilog.php:19 testing error messages (BOOL) false
[2018-07-23 08:07:36] testing_logs.CRITICAL:  test-minilog.php:20 testing critical messages (BOOL) false
[2018-07-23 08:07:36] testing_logs.ALERT:     test-minilog.php:21 testing alert messages (BOOL) false
[2018-07-23 08:07:36] testing_logs.EMERGENCY: test-minilog.php:22 testing emergency messages (BOOL) false
```
