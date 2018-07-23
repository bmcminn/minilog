<?php


require './app/Minilog.php';


$logger = new \Gbox\Minilog('testing_logs', [
    'dir'   => '.',
    'linenos' => true,
]);


$logger->info('testing', 'string', 'concatenation');

$logger->debug('testing debug messages', true);
$logger->info('testing info messages', true);
$logger->notice('testing notice messages', true);
$logger->warning('testing warning messages', false);
$logger->error('testing error messages', false);
$logger->critical('testing critical messages', false);
$logger->alert('testing alert messages', false);
$logger->emergency('testing emergency messages', false);


$logger->info('testing', 'boolean interpolation', true);


$arr = [true, false, 'test', 1001.123, 0, -132456];


$logger->info('testing', 'array interpolation', $arr);


$logger->info('testing', 'object interpolation', $logger);


$logger->info('testing', 'is', 'complete', true);

echo getcwd();

echo implode(' ', ['afsdjkl', 'sdjfsld', true]);
