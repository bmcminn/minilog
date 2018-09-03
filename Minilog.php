<?php

namespace Gbox;

class Minilog {

    private $dateFormat  = 'Y-m-d';

    private $levels;
    private $name;
    private $linenos;
    private $timestamp;
    private $filepath;
    private $logLevel;
    private $console;

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
    function __construct($name, $opts) {

        $this->name = $name;

        $defaults = [
            'console'   => true,
            'dir'       => '.',
            'level'     => 'DEBUG',
            'linenos'   => false,
            'timestamp' => '[Y-m-d H:m:s]',
        ];

        $opts = array_replace_recursive($defaults, $opts);

        $opts['level'] = strtoupper($opts['level']);

        // TODO: validate the user defined one of the given log levels
        $this->levels = [
            'DEBUG'     => [ 100, "\e[0;30;42m" ],     // Detailed debug information.
            'INFO'      => [ 200, "\e[0m" ],     // Interesting events. Examples: User logs in, SQL logs.
            'NOTICE'    => [ 250, "\e[1;36;40m" ],     // Normal but significant events.
            'WARNING'   => [ 300, "\e[1;33;93m" ],     // Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
            'ERROR'     => [ 400, "\e[91m" ],     // Runtime errors that do not require immediate action but should typically be logged and monitored.
            'CRITICAL'  => [ 500, "\e[45m" ],     // Critical conditions. Example: Application component unavailable, unexpected exception.
            'ALERT'     => [ 550, "\e[0;30;43m" ],     // Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
            'EMERGENCY' => [ 600, "\e[41m" ],     // Emergency: system is unusable.
        ];

        $this->level        = $this->levels[$opts['level']][0];
        $this->filepath     = $opts['dir'] . '/' . date($this->dateFormat) . '-' . $this->name . '.log';
        $this->timestamp    = $opts['timestamp'];
        $this->linenos      = $opts['linenos'];
        $this->console      = $opts['console'];
    }


    /**
     * Appends the given log data to a row in the log file
     * @param  [type] $type [description]
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private function _logger($label, $weight, ...$args) {

        // ignore logging anything below the desired log level
        if ($weight < $this->logLevel) { return; }

        // init our log message
        $msg = [
            date($this->timestamp),     // log timestamp
            str_pad("{$this->name}.{$label}:", strlen($this->name . '.emergency:')),   // log name and message type annotations
        ];

        // log the call file and line number on Debug statements or if the logger logs all line numbers
        if ($label === 'DEBUG' || $this->linenos) {
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

            $dirPath = dirname($this->filepath);

            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            file_put_contents($this->filepath, $msg . PHP_EOL, FILE_APPEND | LOCK_EX);

            if ($this->console) {

                $color = $this->levels[$label][1];

                $this->_print($color . $msg . "\e[0m" . PHP_EOL);
            }

        } catch (Exception $e) {
            $this->_print('Caught exception: ' . $e->getMessage() . PHP_EOL);

        }

        return $msg;
    }


    /**
     * Determines the current SAPI process type and echos to the appropriate console stream
     * @param  string $msg The message to be written to the console
     * @return null
     */
    private function _print($msg) {
        if (php_sapi_name() === 'cli-server') {
            file_put_contents("php://stdout", $msg);

        } else {
            echo $msg;

        }
    }


    public function debug(...$args) {
        return $this->_logger('DEBUG', 100, $args);
    }


    public function info(...$args) {
        return $this->_logger('INFO', 200, $args);
    }


    public function notice(...$args) {
        return $this->_logger('NOTICE', 250, $args);
    }


    public function warning(...$args) {
        return $this->_logger('WARNING', 300, $args);
    }


    public function error(...$args) {
        return $this->_logger('ERROR', 400, $args);
    }


    public function critical(...$args) {
        return $this->_logger('CRITICAL', 500, $args);
    }


    public function alert(...$args) {
        return $this->_logger('ALERT', 550, $args);
    }


    public function emergency(...$args) {
        return $this->_logger('EMERGENCY', 600, $args);
    }

}
