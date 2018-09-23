<?php
/**
 * Description of Pimvc\Views\Helpers\Gis\Osm\Map
 *
 * @author pierrefromager
 */
namespace Pimvc\Views\Helpers\Gis\Osm;

use Pimvc\Views\Helpers\Gis\Osm\Marker;
use Pimvc\Views\Helpers\Gis\Osm\Options as MapOptions;

class Map
{

    const DEFAULT_HEIGHT = 420;
    const TEMPLATE_PATH = '/Template/';
    const TEMPLATE_PARTIAL = 'Osm.php';

    private $view;

    /**
     * $mapOptions
     *
     * @var Options
     */
    private $mapOptions;

    /**
     * $markers
     *
     * @var Marker[]
     */
    private $markers;
    private $polylines;
    private $layer;

    /**
     * __construct
     *
     * @param type $baseUrl
     * @param Marker[] $markers
     * @return $this
     */
    public function __construct()
    {
        $this->markers = [];
        $this->polylines = [];
        $this->view = new \Pimvc\View();
        $this->view->setFilename($this->templateName());
        return $this;
    }

    /**
     * setLayer
     *
     * @param string $layer
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;
        return $this;
    }

    /**
     * setMarkers
     *
     * @param Marker[] $markers
     */
    public function setMarkers($markers)
    {
        $this->markers = $markers;
        return $this;
    }

    /**
     * setPolylines
     *
     * @param Marker[] $polylines
     */
    public function setPolylines($polylines)
    {
        $this->polylines = $polylines;
        return $this;
    }

    /**
     * setOptions
     *
     * @param MapOptions $options
     */
    public function setOptions(MapOptions $options)
    {
        $this->mapOptions = $options;
        return $this;
    }

    /**
     * render
     *
     */
    public function render()
    {
        $this->view->setParams($this->params());
        $this->view->render();
        return $this;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->view;
    }

    /**
     * options
     *
     * @return MapOptions
     */
    public function options()
    {
        return $this->mapOptions;
    }

    /**
     * params
     *
     * @return array
     */
    protected function params()
    {
        return [
            'mapHeight' => self::DEFAULT_HEIGHT
            , 'options' => $this->mapOptions
            , 'markersJson' => $this->getJsonMarkers()
            , 'polylinesJson' => \json_encode($this->polylines, JSON_PRETTY_PRINT)
            , 'layer' => $this->layer
        ];
    }

    /**
     * getJsonMarkers
     *
     * @return string
     */
    protected function getJsonMarkers()
    {
        $jsonMarker = [];
        foreach ($this->markers as $marker) {
            $jsonMarker[] = $marker->get(false);
        }
        return \json_encode($jsonMarker, JSON_PRETTY_PRINT);
    }

    /**
     * templateName
     *
     * @return string
     */
    protected function templateName()
    {
        return __DIR__ . self::TEMPLATE_PATH . self::TEMPLATE_PARTIAL;
    }
}
