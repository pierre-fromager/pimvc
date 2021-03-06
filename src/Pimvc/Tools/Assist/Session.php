<?php

/**
 * Description of Pimvc\Tools\Assist\Session
 *
 * @author pierrefromager
 */
namespace Pimvc\Tools\Assist;

use Pimvc\Tools\Session as sessionTools;

class Session
{
    const ASSIST_CALLBACK = 'strlen';
    const ASSIST_DEBUG = false;

    private static $map = [];

    /**
     * setMap
     *
     * @param array $map
     */
    public static function setMap($map)
    {
        self::$map = $map;
    }

    /**
     * get
     *
     * @param string $name
     * @param \Pimvc\Http\Request $request
     * @param boolean $reset
     *
     * @return array
     */
    public static function get($name, \Pimvc\Http\Request $request, $reset = false)
    {
        $assist = [];
        $datas = $request->getParams();
        $isPost = ($request->getMethod() === 'POST');
        $persitentDatas = sessionTools::get($name);
        $storeData = [];
        if ($isPost || self::$map) {
            $preparedData = ($isPost)
                ? $datas
                : array_intersect_key($datas, array_flip(self::$map));
            if ($preparedData) {
                $storeData = array_merge(
                    (array) $persitentDatas,
                    array_filter($preparedData, 'strlen')
                );
            }
            if ($reset) {
                $storeData[$reset] = '';
            }
            if ($storeData) {
                sessionTools::set($name, $storeData);
            }
        }
        $assist = (sessionTools::has($name)) ? sessionTools::get($name) : [];
        
        if (self::ASSIST_DEBUG) {
            $registry = Registry::getInstance();
            $firePhp = $registry->getObject('FirePhp');
            $firePhp->setEnabled(true);
            $firePhp->info('Assist', serialize($assist));
            $firePhp->info('storeData', serialize($storeData));
            $firePhp->info('Reset', $reset);
            unset($firePhp);
            unset($registry);
        }

        return $assist;
    }
    
    /**
     * getDatas
     *
     * @param string $name
     * @return mixed
     */
    public static function getDatas($name)
    {
        return sessionTools::get($name);
    }


    /**
     * getSearch
     *
     * @param string $name
     * @param \Request $request
     * @param boolean $reset
     * @param string $key
     * @return array
     */
    public static function getSearch($name, $request, $reset = false, $key = '')
    {
        $directDatas = $isPost = false;
        if ($request instanceof \Pimvc\Http\Request) {
            $params = $request->get()['request'];
            $isPost = $request->getMethod() === 'POST';
        } else {
            $params = $request;
            $directDatas = true;
        }
        if ($isPost || $directDatas) {
            $postedData = array_filter($params, 'strlen');
            self::set($name, $postedData, $key);
        }
        $assist = (sessionTools::has($name, $key))
            ? sessionTools::get($name, $key)
            : [];
        if ($reset) {
            $assist = [];
            self::set($name, $assist);
        }
        return $assist;
    }
    
    /**
     * set
     *
     * @param string $name
     * @param array $data
     * @param array $key
     */
    public static function set($name, $data, $key = '')
    {
        sessionTools::set($name, $data, $key);
    }
    
    /**
     * has
     *
     * @param string $name
     * @param \Rquest $request
     * @param string $key
     * @return boolean
     */
    public static function has($name, $request, $key)
    {
        $assist = self::get($name, $request);
        return sessionTools::has($assist[$key]);
    }
    
    /**
     * delete
     *
     * @param string $name
     * @param string $key
     */
    public static function delete($name, $key)
    {
        sessionTools::delete($name, $key);
    }

    /**
     * debug
     *
     * @param string $name
     * @param \Request $request
     */
    public static function debug($name, $request)
    {
        $assist = self::get($name, $request);
        var_dump($assist);
    }
}
