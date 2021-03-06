<?php

/**
 * class Logger
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc;

class Logger implements Interfaces\Logger
{

    /**
     * Current status of the log file
     * @var integer
     */
    private $_logStatus = self::STATUS_LOG_CLOSED;

    /**
     * Holds messages generated by the class
     * @var array
     */
    private $_messageQueue = [];

    /**
     * Path to the log file
     * @var string
     */
    private $_logFilePath = null;

    /**
     * Current minimum logging threshold
     * @var integer
     */
    private $_severityThreshold = self::DEBUG;

    /**
     * This holds the file handle for this instance's log file
     * @var resource
     */
    private $_fileHandle = null;

    /**
     * Standard messages produced by the class. Can be modified for il8n
     * @var array
     */
    private $_messages = array(
        'writefail' => 'The file could not be written to. Check that appropriate permissions have been set.',
        'opensuccess' => 'The log file was opened successfully.',
        'openfail' => 'The file could not be opened. Check permissions.',
    );

    /**
     * Default severity of log messages, if not specified
     * @var integer
     */
    private static $_defaultSeverity = self::DEBUG;

    /**
     * Valid PHP date() format string for log timestamps
     * @var string
     */
    private static $_dateFormat = 'Y-m-d H:i:s';

    /**
     * Octal notation for default permissions of the log file
     * @var integer
     */
    private static $_defaultPermissions = 0777;

    /**
     * Array of KLogger instances, part of Singleton pattern
     * @var array
     */
    private static $fileInstances = [];
    private static $dbInstances = [];
    private static $remoteAddr = '';
    private static $adapter;
    private static $filename;

    /**
     * Partially implements the Singleton pattern. Each $logDirectory gets one
     * instance.
     *
     * @param string  $logDirectory File path to the logging directory
     * @param integer $severity     One of the pre-defined severity constants
     * @return \Pimvc\Logger
     */
    public static function getFileInstance($logDirectory = false, $severity = false, $adapter = self::LOG_ADAPTER_FILE)
    {
        self::$adapter = $adapter;
        if ($severity === false) {
            $severity = self::$_defaultSeverity;
        }
        if ($logDirectory === false) {
            if (count(self::$fileInstances) > 0) {
                return current(self::$fileInstances);
            } else {
                $logDirectory = dirname(__FILE__);
            }
        }

        if (in_array($logDirectory, self::$fileInstances)) {
            return self::$fileInstances[$logDirectory];
        }

        self::$fileInstances[$logDirectory] = new self(
            $logDirectory,
            $severity,
            $adapter
        );

        return self::$fileInstances[$logDirectory];
    }

    /**
     * getFilename
     *
     * @return string
     */
    public static function getFilename(): string
    {
        return static::$filename;
    }

    /**
     * getDbInstance
     *
     * @param string $name
     * @param string $severity
     * @return \logger
     */
    public static function getDbInstance(
        $name,
        $severity = false,
        $adapter = self::LOG_ADAPTER_DB
    ) {
        self::$adapter = $adapter;
        if (count(self::$dbInstances) > 0) {
            $isInstanciated = (in_array($name, self::$dbInstances));
            if ($isInstanciated) {
                return self::$dbInstances[$name];
            }
        }
        self::$dbInstances[$name] = new self(
            $name,
            ($severity === false)
                ? self::$_defaultSeverity
                : $severity,
            self::$adapter
        );
        return self::$dbInstances[$name];
    }

    /**
     * Class constructor
     *
     * @param string  $logDirectory File path to the logging directory
     * @param integer $severity     One of the pre-defined severity constants
     * @return void
     */
    private function __construct($logDirectory, $severity, $adapter)
    {
        self::$remoteAddr = (php_sapi_name() === 'cli')
            ? 'localhost'
            : $_SERVER['REMOTE_ADDR'];
        if ($severity === self::OFF) {
            return;
        }
        if ($adapter == self::LOG_ADAPTER_FILE) {
            $logDirectory = rtrim($logDirectory, '\\/');
            $this->_logFilePath = $logDirectory
                . DIRECTORY_SEPARATOR
                . 'log_'
                . date('Y-m-d')
                . '.txt';
            self::$filename = $this->_logFilePath;
            $this->_severityThreshold = $severity;
            if (!file_exists($logDirectory)) {
                $mkResult = @mkdir($logDirectory, self::$_defaultPermissions, true);
                if (!$mkResult) {
                    throw new \Exception('Logger can not make directory ' . $logDirectory);
                }
            }
            if (file_exists($this->_logFilePath) && !is_writable($this->_logFilePath)) {
                $this->_logStatus = self::STATUS_OPEN_FAILED;
                $this->_messageQueue[] = $this->_messages['writefail'];
                throw new \Exception('Logger can not write file ' . $this->_logFilePath);
            }
            if (($this->_fileHandle = fopen($this->_logFilePath, 'a'))) {
                $this->_logStatus = self::STATUS_LOG_OPEN;
                $this->_messageQueue[] = $this->_messages['opensuccess'];
            } else {
                $this->_logStatus = self::STATUS_OPEN_FAILED;
                $this->_messageQueue[] = $this->_messages['openfail'];
            }
        }
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        if ($this->_fileHandle) {
            fclose($this->_fileHandle);
        }
    }

    /**
     * Writes a $line to the log with a severity level of DEBUG
     *
     * @param string $line Information to log
     * @return void
     */
    public function logDebug($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::DEBUG, $args);
    }

    /**
     * Returns (and removes) the last message from the queue.
     * @return string
     */
    public function getMessage()
    {
        return array_pop($this->_messageQueue);
    }

    /**
     * Returns the entire message queue (leaving it intact)
     * @return array
     */
    public function getMessages()
    {
        return $this->_messageQueue;
    }

    /**
     * Empties the message queue
     * @return void
     */
    public function clearMessages()
    {
        $this->_messageQueue = [];
    }

    /**
     * Sets the date format used by all instances of KLogger
     *
     * @param string $dateFormat Valid format string for date()
     */
    public static function setDateFormat($dateFormat)
    {
        self::$_dateFormat = $dateFormat;
    }

    /**
     * Writes a $line to the log with a severity level of INFO. Any information
     * can be used here, or it could be used with E_STRICT errors
     *
     * @param string $line Information to log
     * @return void
     */
    public function logInfo($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::INFO, $args);
    }

    /**
     * Writes a $line to the log with a severity level of NOTICE. Generally
     * corresponds to E_STRICT, E_NOTICE, or E_USER_NOTICE errors
     *
     * @param string $line Information to log
     * @return void
     */
    public function logNotice($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::NOTICE, $args);
    }

    /**
     * Writes a $line to the log with a severity level of WARN. Generally
     * corresponds to E_WARNING, E_USER_WARNING, E_CORE_WARNING, or
     * E_COMPILE_WARNING
     *
     * @param string $line Information to log
     * @return void
     */
    public function logWarn($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::WARN, $args);
    }

    /**
     * Writes a $line to the log with a severity level of ERR. Most likely used
     * with E_RECOVERABLE_ERROR
     *
     * @param string $line Information to log
     * @return void
     */
    public function logError($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::ERR, $args);
    }

    /**
     * Writes a $line to the log with a severity level of FATAL. Generally
     * corresponds to E_ERROR, E_USER_ERROR, E_CORE_ERROR, or E_COMPILE_ERROR
     *
     * @param string $line Information to log
     * @return void
     * @deprecated Use logCrit
     */
    public function logFatal($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::FATAL, $args);
    }

    /**
     * Writes a $line to the log with a severity level of ALERT.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logAlert($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::ALERT, $args);
    }

    /**
     * Writes a $line to the log with a severity level of CRIT.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logCrit($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::CRIT, $args);
    }

    /**
     * Writes a $line to the log with a severity level of EMERG.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logEmerg($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::EMERG, $args);
    }

    /**
     * Writes a $line to the log with the given severity
     *
     * @param string  $line     Text to add to the log
     * @param integer $severity Severity level of log message (use constants)
     */
    public function log($line, $severity, $args = self::NO_ARGUMENTS)
    {
        if ($this->_severityThreshold >= $severity) {
            $status = $this->_getTimeLine($severity);
            if (self::$adapter == self::LOG_ADAPTER_FILE) {
                $line = "$status $line";
                if ($args !== self::NO_ARGUMENTS) {
                    $isArgString = (is_string($args));
                    $argLine = ($isArgString) ? $args : var_export($args, true);
                    $argLine = (!$isArgString) ? str_replace(
                        ["\n", ' '],
                        '',
                        $argLine
                    ) : $argLine;
                    $line = $line . ';' . $argLine;
                }
                $this->writeFreeFormLine($line . PHP_EOL);
            } elseif ($this->isDbAdapter(self::$adapter)
                    && !$this->isLan()
                ) {
                $logModel = new Model_Logs();
                $timeLine = explode(';', $status);
                $type = trim($timeLine[2]);
                $uid = Tools_Session::getUid();
                $uid = empty($uid) ? 0 : Tools_Session::getUid();
                $params = array(
                    'ip' => self::$remoteAddr
                    , 'uid' => $uid
                    , 'time' => date(self::$_dateFormat)
                    , 'type' => $type
                    , 'source' => $line
                    , 'message' => var_export($args, true)
                );
                $logModel->insert($params, true); // true to forget primary
                unset($logModel);
            }
        }
    }
    
    /**
     * isDbAdapter
     *
     * @param string $adapterPool
     * @return boolean
     */
    private function isDbAdapter($adapterPool)
    {
        $dbAdaptersPool = array(
            self::LOG_ADAPTER_DB
            , self::LOG_ADAPTER_DB1
            , self::LOG_ADAPTER_DB2
            , self::LOG_ADAPTER_DB3
            , self::LOG_ADAPTER_DB4
        );
        return in_array($adapterPool, $dbAdaptersPool);
    }


    /**
     * isLan
     *
     * @return type
     */
    private function isLan()
    {
        return false;
        return (self::LOG_LAN_ANY)
            ? false
            : (strpos($_SERVER['REMOTE_ADDR'], self::LOG_LAN_PREFIX) !== false);
    }

    /**
     * Writes a line to the log without prepending a status or timestamp
     *
     * @param string $line Line to write to the log
     * @return void
     */
    public function writeFreeFormLine($line)
    {
        if ($this->_logStatus == self::STATUS_LOG_OPEN
                && $this->_severityThreshold != self::OFF) {
            if (fwrite($this->_fileHandle, $line) === false) {
                $this->_messageQueue[] = $this->_messages['writefail'];
            }
        }
    }

    /**
     * _getTimeLine
     *
     * @param type $level
     * @return type
     */
    private function _getTimeLine($level)
    {
        $time = self::$remoteAddr . ' ; ' . date(self::$_dateFormat) . ' ; ';
        switch ($level) {
            case self::EMERG:
                return $time .  'EMERG;';
            case self::ALERT:
                return $time . 'ALERT;';
            case self::CRIT:
                return $time . 'CRIT;';
            case self::FATAL: # FATAL is an alias of CRIT
                return $time . 'FATAL;';
            case self::NOTICE:
                return $time . 'NOTICE;';
            case self::INFO:
                return $time . 'INFO;';
            case self::WARN:
                return $time . 'WARN;';
            case self::DEBUG:
                return $time . 'DEBUG;';
            case self::ERR:
                return $time . 'ERROR;';
            default:
                return $time . 'LOG;';
        }
    }
}
