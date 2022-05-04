<?php

namespace Gbox;

class Minilog {

    const DS = DIRECTORY_SEPARATOR;

    private static $dateFormat  = 'Y-m-d';

    private static $level;
    private static $levels;
    private static $name;
    private static $linenos;
    private static $timestamp;
    private static $filepath;
    private static $logLevel;
    private static $console;


    private static $instance;


    /**
     * Constructor for new logger instances
     * @param string    $name               defines the log file
     * @param array     $opts               collection of properties that define the behavior of the logger
     * @param bool      $opts['console']    defaults true;    defines where log entries should echo to the console
     * @param string    $opts['dir']        defaults '.';     defines where log files should be written to
     * @param string    $opts['level']      defaults 'DEBUG'; defines which RFC 5424 levels to log
     * @param bool      $opts['linenos']    defaults true;    defines whether to log the path and line number of the log call
     * @param string    $opts['timestamp']  defines the timestamp format
     */
    public static function setup($name, $opts = []) {

        self::$name = $name;

        $defaults = [
            'console'   => true,
            'dir'       => '',
            'level'     => 'DEBUG',
            'linenos'   => false,
            'timestamp' => '[Y-m-d H:m:s]',
        ];

        $opts = array_replace_recursive($defaults, $opts);

        $opts['level'] = strtoupper($opts['level']);

        // TODO: validate the user defined one of the given log levels
        self::$levels = [
            'DEBUG'     => [ 100, "\e[0;30;42m" ],  // Detailed debug information.
            'INFO'      => [ 200, "\e[0m" ],        // Interesting events. Examples: User logs in, SQL logs.
            'NOTICE'    => [ 250, "\e[1;36;40m" ],  // Normal but significant events.
            'WARNING'   => [ 300, "\e[1;33;93m" ],  // Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
            'ERROR'     => [ 400, "\e[91m" ],       // Runtime errors that do not require immediate action but should typically be logged and monitored.
            'CRITICAL'  => [ 500, "\e[45m" ],       // Critical conditions. Example: Application component unavailable, unexpected exception.
            'ALERT'     => [ 550, "\e[0;30;43m" ],  // Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
            'EMERGENCY' => [ 600, "\e[41m" ],       // Emergency: system is unusable.
        ];

        self::$level        = self::$levels[$opts['level']][0];
        self::$filepath     = getcwd() . self::DS . $opts['dir'] . self::DS . self::$name . '-' . date(self::$dateFormat) . '.log';
        self::$timestamp    = $opts['timestamp'];
        self::$linenos      = $opts['linenos'];
        self::$console      = $opts['console'];
    }


    /**
     * Appends the given log data to a row in the log file
     * @param  [type] $type [description]
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private static function _logger($label, $weight, ...$args) {

        // ignore logging anything below the desired log level
        if ($weight < self::$logLevel) { return; }

        // init our log message
        $msg = [
            date(self::$timestamp),     // log timestamp
            str_pad(self::$name . ".{$label}:", strlen(self::$name . '.emergency:')),   // log name and message type annotations
        ];

        // log the call file and line number on Debug statements or if the logger logs all line numbers
        if ($label === 'DEBUG' || self::$linenos) {
            $debug = debug_backtrace()[1];

            $debug['file'] = substr($debug['file'], strlen(getcwd()) + 1);
            array_push($msg, "{$debug['file']}:{$debug['line']}");
        }

        // iterate over each arguemnt in the log and format it accordingly
        foreach ($args[0] as $arg) {

            // automatically JSON encode arrays and objects
            if (is_array($arg) || is_object($arg)) {
                array_push($msg, '(JSON)');
                $arg = json_encode($arg);
            }

            // convert booleans to text equivalents
            if (is_bool($arg)) {
                array_push($msg, '(BOOL)');
                $arg = $arg ? 'true' : 'false';
            }

            array_push($msg, $arg);
        }

        // construct log line
        $msg = implode(' ', $msg);

        // write file to disk and append log message
        try {

            $dirPath = dirname(self::$filepath);

            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            file_put_contents(self::$filepath, $msg . PHP_EOL, FILE_APPEND | LOCK_EX);

            if (self::$console) {

                $color = self::$levels[$label][1];

                self::_print($color . $msg . "\e[0m" . PHP_EOL);
            }

        } catch (Exception $e) {
            self::_print('Caught exception: ' . $e->getMessage() . PHP_EOL);

        }

        return $msg;
    }


    /**
     * Determines the current SAPI process type and echos to the appropriate console stream
     * @param  string $msg The message to be written to the console
     * @return null
     */
    private static function _print($msg) {
        if (php_sapi_name() === 'cli-server') {
            file_put_contents("php://stdout", $msg);

        } else {
            echo $msg;

        }
    }


    public static function debug(...$args) {
        return self::_logger('DEBUG', 100, $args);
    }


    public static function info(...$args) {
        return self::_logger('INFO', 200, $args);
    }


    public static function notice(...$args) {
        return self::_logger('NOTICE', 250, $args);
    }


    public static function warning(...$args) {
        return self::_logger('WARNING', 300, $args);
    }


    public static function error(...$args) {
        return self::_logger('ERROR', 400, $args);
    }


    public static function critical(...$args) {
        return self::_logger('CRITICAL', 500, $args);
    }


    public static function alert(...$args) {
        return self::_logger('ALERT', 550, $args);
    }


    public static function emergency(...$args) {
        return self::_logger('EMERGENCY', 600, $args);
    }

}
