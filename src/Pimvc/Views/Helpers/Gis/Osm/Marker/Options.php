<?php
/*
 * Pimvc\Views\Helpers\Gis\Osm\Marker\Options
 */
namespace Pimvc\Views\Helpers\Gis\Osm\Marker;

use Pimvc\Views\Helpers\Gis\Osm\Marker\Icon as MarkerIcon;

class Options
{

    const DEFAULT_PANE = 'markerPane';

    public $icon;
    public $keyboard;
    public $title;
    public $alt;
    public $zIndexOffset;
    public $opacity;
    public $riseOnHover;
    public $riseOffset;
    public $pane;
    public $bubblingMouseEvents;
    

    public function __construct(MarkerIcon $icon)
    {
        $this->init($icon);
    }

    private function init(MarkerIcon $icon)
    {
        $this->icon = $icon;
        $this->keyboard = true;
        $this->title = '';
        $this->alt = '';
        $this->zIndexOffset = 0;
        $this->opacity = 1;
        $this->riseOffset = 250;
        $this->riseOnHover = true;
        $this->pane = self::DEFAULT_PANE;
        $this->bubblingMouseEvents = false;
    }

    public function render()
    {
        $that = (object) array_filter((array) $this);
        return \json_encode($that, JSON_PRETTY_PRINT);
    }
}
