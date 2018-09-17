<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Pimvc\Views\Helpers\Gis\Osm\Marker;

class Icon
{

    const ME_IMG_ICON = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';

    public $iconUrl;
    public $shadowUrl;
    public $iconSize;
    public $iconAnchor;
    public $shadowAnchor;
    public $popupAnchor;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->iconUrl = self::ME_IMG_ICON;
        $this->shadowUrl = '';
        $this->iconSize = [26, 46];
        $this->iconAnchor = [12, 46];
        $this->shadowAnchor = [0, 0];
        $this->popupAnchor = [12, 0];
    }
}
