<?php

namespace Pimvc;

/**
 * Description of config
 *
 * @author pierrefromager
 */

class Config implements \Pimvc\Interfaces\Config
{
    protected $path;
    protected $env;
    protected $settings;

    /**
     * __construct
     *
     * @param string $env
     */
    public function __construct($env = self::ENV_DEV)
    {
        $this->setEnv($env);
        return $this;
    }
    
    /**
     * setEnv
     *
     * @param string $env
     * @return $this
     */
    public function setEnv($env = self::ENV_DEV)
    {
        $this->env = $env;
        return $this;
    }
    
    /**
     * setPath
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
    
    /**
     * getPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * getSettings
     *
     * @param string $key
     * @return mixed
     */
    public function getSettings($key = '')
    {
        return ($key) ? $this->settings[$key] : $this->settings;
    }
    
    /**
     * hasEntry
     *
     * @param string $key
     * @return boolean
     */
    public function hasEntry($key)
    {
        return isset($this->settings[$key]);
    }
    
    /**
     * load
     *
     * @return $this
     */
    public function load()
    {
        $filename = $this->getFilename();
        if (!$this->check($filename)) {
            throw new \Exception(self::CONFIG_ERROR_MISSING . $this->env);
        }
        $this->settings = require $this->getFilename();
        return $this;
    }
    
    /**
     * getFilename
     *
     * @return string
     */
    private function getFilename()
    {
        return $this->path . $this->env . '.php';
    }
    
    /**
     * check
     *
     * @param string $filename
     * @return boolean
     */
    private function check($filename)
    {
        return (
            in_array($this->env, $this->getAllowedEnv())
            && file_exists($filename)
        );
    }
    
    /**
     * getAllowedEnv
     *
     * @return array
     */
    private function getAllowedEnv()
    {
        return [
            self::ENV_DEV, self::ENV_INT, self::ENV_PROD,
            self::ENV_TEST, self::ENV_CLI
        ];
    }
}
