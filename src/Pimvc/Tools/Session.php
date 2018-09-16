<?php

/**
 * Description of Pimvc\Tools\Session
 *
 * provides methods to get or set session datas.
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Tools;

class Session
{
    const SESSION_ADMIN = 'admin';
    const SESSION_MANAGER = 'manager';
    const SESSION_ANONYMOUS = 'anonymous';
    const SESSION_ID = 'id';
    const SESSION_PROFIL = 'profil';
    const SESSION_USER_INFO = 'userinfo';

    private static $baseUrl;

    /**
     * start
     *
     */
    public static function start()
    {
        self::$baseUrl = \Pimvc\App::getInstance()->getRequest()->getBaseUrl();
        session_name(md5(self::$baseUrl));
        session_start();
    }

    /**
     * getUid
     *
     * @return int
     */
    public static function getUid()
    {
        return (isset($_SESSION[self::SESSION_ID])) ? $_SESSION[self::SESSION_ID] : '';
    }

    /**
     * isAuth
     *
     * @return boolean
     */
    public static function isAuth()
    {
        $uid = self::getUid();
        return (!empty($uid));
    }

    /**
     * getProfil
     *
     * @return string
     */
    public static function getProfil()
    {
        return (isset($_SESSION[self::SESSION_PROFIL])) ? $_SESSION[self::SESSION_PROFIL] : self::SESSION_ANONYMOUS;
    }

    /**
     * getUserInfo
     *
     * @return string
     */
    public static function getUserInfo()
    {
        return (isset($_SESSION[self::SESSION_USER_INFO])) ? $_SESSION[self::SESSION_USER_INFO] : self::SESSION_ANONYMOUS;
    }

    /**
     * isAdmin
     *
     * @return boolean
     */
    public static function isAdmin()
    {
        return (self::isAuth() && self::getProfil() == self::SESSION_ADMIN);
    }

    /**
     * isManager
     *
     * @return boolean
     */
    public static function isManager()
    {
        return (self::isAuth() && self::getProfil() == self::SESSION_MANAGER);
    }

    /**
     * getBaseUrl
     *
     * @return string
     */
    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    /**
     * isMine
     *
     * @param int $uid
     * @return boolean
     */
    public static function isMine($uid)
    {
        return (self::isAuth() && $uid == self::getUid());
    }

    /**
     * set
     *
     * @param string $name
     * @param mixed $value
     * @param string $key
     */
    public static function set($name, $value, $key = '')
    {
        if ($key) {
            $_SESSION[$name][$key] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
    }

    /**
     * delete
     *
     * @param string $name
     * @param string $key
     */
    public static function delete($name, $key = '')
    {
        if ($key) {
            unset($_SESSION[$name][$key]);
        } else {
            unset($_SESSION[$name]);
        }
    }

    /**
     * has
     *
     * @param string $name
     * @param string $key
     * @return boolean
     */
    public static function has($name, $key = '')
    {
        $has = false;
        if (empty($key)) {
            $has = (isset($_SESSION[$name]) && !empty($_SESSION[$name]));
        } else {
            $has = (isset($_SESSION[$name][$key]) && !empty($_SESSION[$name][$key]));
        }
        return $has;
    }

    /**
     * get
     *
     * @param string $name
     * @param string $key
     * @return mixed
     */
    public static function get($name, $key = '')
    {
        if (empty($key)) {
            $value = (self::has($name)) ? $_SESSION[$name] : '';
        } else {
            $value = (self::has($name, $key)) ? $_SESSION[$name][$key] : '';
        }
        return $value;
    }

    /**
     * deAuth
     *
     */
    public static function deAuth()
    {
        unset($_SESSION[self::SESSION_ID]);
        unset($_SESSION[self::SESSION_PROFIL]);
        unset($_SESSION[self::SESSION_USER_INFO]);
    }

    /*
    public static function __callStatic($name, $arguments) {
        //var_dump($name, $arguments);
          if (self::$instance === null) {
          self::$instance = new self();
          }

          self::$instance->callStaticMethod($name, $arguments);
    }*/
}
