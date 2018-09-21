<?php
/**
 * Pimvc\Views\Helpers\Gis\Osm\Layer\Tile
 */
namespace Pimvc\Views\Helpers\Gis\Osm\Layer;

class Tile 
{

    const _BASIC = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    const _WIKIMEDIA = 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png';
    const _HUMANITARIAL = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
    const _OSM_FRANCE = 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
    const _OSM_FOREST = 'https://tile.thunderforest.com/landscape/{z}/{x}/{y}.png';
    const _OSM_FOREST_OUTDOOR = 'https://tile.thunderforest.com/outdoors/{z}/{x}/{y}.png';
    const _OSM_TRANSPORTATION = 'https://tile.thunderforest.com/transport/{z}/{x}/{y}.png';
    const _OSM_BW = 'http://a.tile.stamen.com/toner/{z}/{x}/{y}.png';
    const _OPENRAILWAY_STD = 'https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png';
    const _OPENRAILWAY_SPEED = 'https://{s}.tiles.openrailwaymap.org/maxspeed/{z}/{x}/{y}.png';
    const _PIER_INFOR_OVH = 'http://osm.pier-infor.fr/{z}/{x}/{y}.png';

    private $url;

    /**
     * __construct
     *
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->url = ($url) ? $url : self::_BASIC;
    }

    /**
     * getUrl
     *
     * @return type
     */
    public function getUrl()
    {
        return $this->url;
    }
}
