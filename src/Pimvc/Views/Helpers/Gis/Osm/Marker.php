<?php

/**
 * Description of Pimvc\Views\Helpers\Gis\Osm\Marker
 *
 * @author pierrefromager
 */
namespace Pimvc\Views\Helpers\Gis\Osm;

use Pimvc\Views\Helpers\Gis\Osm\Marker\Options as MarkerOptions;

class Marker
{

    protected $lat;
    protected $lon;
    protected $options;

    /**
     * __construct
     *
     * @param MarkerOptions $options
     */
    public function __construct(MarkerOptions $options)
    {
        $this->init($options);
    }

    /**
     * setLatlng
     *
     * @param float $lat
     * @param float $lon
     */
    public function setLatlng(float $lat, float $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    /**
     * getLatLng
     *
     * @param bool $asJson
     * @return string | array
     */
    public function getLatLng(bool $asJson = false)
    {
        $v = [$this->getLat(), $this->getLng()];
        return (!$asJson) ? $v : \json_encode($v);
    }

    /**
     * getLat
     *
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * getLng
     *
     * @return float
     */
    public function getLng(): float
    {
        return $this->lon;
    }

    /**
     * getOptions
     *
     * @return MarkerOptions
     */
    public function getOptions(): MarkerOptions
    {
        return $this->options;
    }

    /**
     * init
     *
     * @param MarkerOptions $options
     */
    private function init(MarkerOptions $options)
    {
        $this->options = $options;
    }
}
