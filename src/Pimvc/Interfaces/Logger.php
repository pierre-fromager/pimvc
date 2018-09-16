<?php

/**
 * class Logger
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Interfaces;

interface Logger
{

    /**
     *
     * Error severity, from low to high. From BSD syslog RFC, secion 4.1.1
     * @link http://www.faqs.org/rfcs/rfc3164.html
     */
    const EMERG = 0;  // Emergency: system is unusable
    const ALERT = 1;  // Alert: action must be taken immediately
    const CRIT = 2;  // Critical: critical conditions
    const ERR = 3;  // Error: error conditions
    const WARN = 4;  // Warning: warning conditions
    const NOTICE = 5;  // Notice: normal but significant condition
    const INFO = 6;  // Informational: informational messages
    const DEBUG = 7;  // Debug: debug messages
    const LOG_LAN_PREFIX = '192.';
    const LOG_LAN_ANY = true;
    //log adapter
    const LOG_ADAPTER_FILE = 'file';
    const LOG_ADAPTER_FILE_PATH = 'cache/log/';
    const LOG_ADAPTER_DB = 'db';
    const LOG_ADAPTER_DB1 = 'db1';
    const LOG_ADAPTER_DB2 = 'db2';
    const LOG_ADAPTER_DB3 = 'db3';
    const LOG_ADAPTER_DB4 = 'db4';
    //custom logging level
    /**
     * Log nothing at all
     */
    const OFF = 8;

    /**
     * Alias for CRIT
     * @deprecated
     */
    const FATAL = 2;

    /**
     * Internal status codes
     */
    const STATUS_LOG_OPEN = 1;
    const STATUS_OPEN_FAILED = 2;
    const STATUS_LOG_CLOSED = 3;

    /**
     * We need a default argument value in order to add the ability to easily
     * print out objects etc. But we can't use NULL, 0, FALSE, etc, because those
     * are often the values the developers will test for. So we'll make one up.
     */
    const NO_ARGUMENTS = 'KLogger::NO_ARGUMENTS';

    public static function getFileInstance($logDirectory = false, $severity = false, $adapter = self::LOG_ADAPTER_FILE);

    public static function getDbInstance($name, $severity = false, $adapter = self::LOG_ADAPTER_DB);

    public function logDebug($line, $args = self::NO_ARGUMENTS);

    public function getMessage();

    public function getMessages();

    public function clearMessages();

    public static function setDateFormat($dateFormat);

    public function logInfo($line, $args = self::NO_ARGUMENTS);

    public function logNotice($line, $args = self::NO_ARGUMENTS);

    public function logWarn($line, $args = self::NO_ARGUMENTS);

    public function logError($line, $args = self::NO_ARGUMENTS);

    public function logFatal($line, $args = self::NO_ARGUMENTS);

    public function logAlert($line, $args = self::NO_ARGUMENTS);

    public function logCrit($line, $args = self::NO_ARGUMENTS);

    public function logEmerg($line, $args = self::NO_ARGUMENTS);

    public function log($line, $severity, $args = self::NO_ARGUMENTS);

    public function writeFreeFormLine($line);
}
