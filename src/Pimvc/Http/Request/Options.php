<?php

/**
 * Description of Pimvc\Http\Request\Options
 *
 * @author pierrefromager
 */

namespace Pimvc\Http\Request;

use Pimvc\Http\Interfaces\Request\Options as requestOptionsInterface;

class Options implements requestOptionsInterface {

    private $config;
    public $scheme;
    public $hostname;

    /**
     * __construct
     * 
     * @return $this
     */
    public function __construct() {
        return $this;
    }

    /**
     * load
     * 
     * @param \Pimvc\Config $config
     * @return $this
     * @throws Exception
     */
    public function load(\Pimvc\Config $config) {
        if ($config->hasEntry(self::OPTIONS_REQUEST)) {
            $this->config = $config->getSettings(self::OPTIONS_REQUEST);
            $this->apply();
            return $this;
        }
        throw new Exception(self::OPTIONS_REQUEST_ERROR_CONFIG);
    }

    /**
     * apply
     * 
     */
    private function apply() {
        $this->hostname = $this->config[self::OPTIONS_HOSTNAME];
        $this->scheme = $this->config[self::OPTIONS_SCHEME];
    }

}
