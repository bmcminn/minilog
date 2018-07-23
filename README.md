# Minilog

A dependency free PHP logging utility that just friggin' works :)


## Installation

```
composer require bmcminn/minilog
```


## Usage:

```php
<?php

require "vendor/autoload.php";

//                  Minilog($logname [, $options[] ])
$Logger = new \Gbox\Minilog('logName', [
    // defines where log entries should echo to the console
    'console'    => true            // bool   : defaults true

    // defines where log files should be written to
    'dir'        => './logs'        // string : defaults '.'

    // defines which RFC 5424 levels to log
    'level'      => 'DEBUG'         // string : defaults 'DEBUG'

    // defines whether to log the path and line number of the log call
    'linenos'    => true            // bool   : defaults true

    // defines the timestamp format
    'timestamp'  => '[Y-m-d H:m:s]' // string :
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
