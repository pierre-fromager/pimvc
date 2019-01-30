<?php
/**
 * Pimvc\Db\Adapter\Mongo
 *
 * Tested on Debian 9 with php 7.3.1 fpm
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 *
 * Mongo server setup
 * @see https://docs.mongodb.com/manual/tutorial/install-mongodb-on-debian/
 * Mongo server credentials
 * @see https://docs.mongodb.com/manual/tutorial/enable-authentication/
 * @see https://docs.mongodb.com/manual/reference/configuration-options/#security.authorization
 * Mongo client test
 * @see https://robomongo.org/download
 * Custom php-fpm setup
 * @see https://www.howtoforge.com/tutorial/how-to-install-php-7-on-debian
 * PECL extension setup inspired
 * @see https://github.com/zakhttp/Mongostead7/blob/master/mongoHomestead7.1.sh
 * For debian update source list with :
 * deb http://repo.mongodb.org/apt/debian stretch/mongodb-org/4.0 main
 *
 * For custom php version
 * sudo pecl download mongodb then untgz then cd in
 * then use appropriate phpize Ex :/opt/php-7.3/bin/phpize
 * ./configure --with-php-config /opt/php-7.3/bin/php-config
 * as sudo make ;make install
 * Edit your php.ini adding extension=mongodb.so
 * MongoDB PHP Library
 * Once done tested ok, add the client in your project with composer :
 * composer require mongodb/mongodb
 * @see https://docs.mongodb.com/php-library/current/
 */

namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Mongo implements IAdapter
{

    const PREFIX_MONGO = 'mongodb://';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * hasValue
     *
     * @param string $name
     * @return boolean
     */
    private static function hasValue($name)
    {
        return (isset(self::$params[$name]) && !empty(self::$params[$name]));
    }

    /**
     * setDsn : returns dsn string
     *
     */
    private static function setDsn()
    {
        $host = self::hasValue(self::_HOST) ? self::$params[self::_HOST] : 'localhost';
        $port = self::hasValue(self::_PORT) ? ':' . self::$params[self::_PORT] : '27017';
        $login = self::hasValue(self::_USER) ? self::$params[self::_USER] : '';
        $password = self::hasValue(self::_PASSWORD) ? self::$params[self::_PASSWORD] : '';
        $credentials = ($login || $password) ? $login . ':' . $password . '@' : '';
        self::$dsn  = self::PREFIX_MONGO . $credentials . $host . $port;
    }
    
    /**
     * getInstance
     *
     * @param array $params
     * @return \MongoDB\Client
     */
    public static function getInstance(array $params)
    {
        self::$params = $params;
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new \MongoDB\Client(self::$dsn);
                self::$_instance->listDatabases();
            } catch (\Exception $e) {
                echo self::ERR_CON_FAIL . $e->getMessage();
                die;
            }
        }
        return self::$_instance;
    }
}
