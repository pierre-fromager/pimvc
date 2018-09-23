<?php
/**
 * Pimvc\Views\Helpers\Gis\Osm\Options
 *
 */
namespace Pimvc\Views\Helpers\Gis\Osm;

class Options
{

    const GMAP_CENTER_PREFIX = 'new google.maps.LatLng(';
    const GMAP_ZOOM = 20;

    public $preferCanvas;
    public $attributionControl;
    public $zoomControl;
    public $closePopupOnClick;
    public $boxZoom;
    public $doubleClickZoom;
    public $dragging;
    public $zoomSnap;
    public $zoomDelta;
    public $trackResize;
    public $inertia;
    public $inertiaDeceleration;
    public $inertiaMaxSpeed;
    public $easeLinearity;
    public $worldCopyJump;
    public $maxBoundsViscosity;
    public $keyboard;
    public $keyboardPanDelta;
    public $scrollWheelZoom;
    public $wheelDebounceTime;
    public $wheelPxPerZoomLevel;
    public $tap;
    public $tapTolerance;
    public $touchZoom;
    public $bounceAtZoomLimits;
    public $crs;
    public $center;
    public $zoom;
    public $minZoom;
    public $maxZoom;
    public $layers;
    protected $latCenter;
    protected $lonCenter;
    protected $boundNorthEast = null;
    protected $boundSouthWest = null;

    /**
     * __construct
     *
     * @param float $latCenter
     * @param float $lonCenter
     */
    public function __construct($latCenter, $lonCenter)
    {
        $this->latCenter = $latCenter;
        $this->lonCenter = $lonCenter;
        $this->init();
    }

    /**
     * init
     *
     */
    private function init()
    {
        $this->preferCanvas = false;
        $this->attributionControl = true;
        $this->zoomControl = true;
        $this->closePopupOnClick = true;
        $this->boxZoom = true;
        $this->doubleClickZoom = true;
        $this->dragging = true;
        $this->zoomSnap = 1;
        $this->zoomDelta = 1;
        $this->trackResize = true;
        $this->inertia = true;
        $this->inertiaDeceleration = 3000;
        $this->inertiaMaxSpeed = INF;
        $this->easeLinearity = 0.2;
        $this->worldCopyJump = false;
        $this->maxBoundsViscosity = 0.0;
        $this->keyboard = true;
        $this->keyboardPanDelta = 80;
        $this->scrollWheelZoom = 40;
        $this->wheelPxPerZoomLevel = 60;
        $this->tap = true;
        $this->tapTolerance = 15;
        $this->touchZoom = 'center';
        $this->bounceAtZoomLimits = true;
        $this->zoom = 15;
    }

    public function setBoundNorthEast(array $boundNorthEast)
    {
        $this->boundNorthEast = $boundNorthEast;
        return $this;
    }

    public function setBoundSouthWest(array $boundSouthWest)
    {
        $this->boundSouthWest = $boundSouthWest;
        return $this;
    }

    public function getBoundNorthEast($asJon = true)
    {
        return ($asJon) ? \json_encode($this->boundNorthEast) : $this->boundNorthEast;
    }

    public function getBoundSouthWest($asJon = true)
    {
        return ($asJon) ? \json_encode($this->boundSouthWest) : $this->boundSouthWest;
    }

    public function isBound()
    {
        return !(is_null($this->boundNorthEast) && is_null($this->boundSouthWest));
    }

    /**
     * center
     *
     * @return array | string
     */
    public function center($asJson = false)
    {
        $c = [$this->latCenter, $this->lonCenter];
        return (!$asJson) ? $c : \json_encode($c);
    }

    /**
     * clat
     *
     * @return float
     */
    public function clat()
    {
        return $this->latCenter;
    }

    /**
     * clon
     *
     * @return float
     */
    public function clon()
    {
        return $this->lonCenter;
    }
}
