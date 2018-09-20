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

    private $baseUrl;
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
    public $markers;
    private $layer;

    /**
     * __construct
     *
     * @param type $baseUrl
     * @param Marker[] $markers
     * @return $this
     */
    public function __construct($baseUrl, $markers, MapOptions $mapOptions)
    {
        $this->baseUrl = $baseUrl;
        $this->markers = $markers;
        $this->mapOptions = $mapOptions;
        $this->view = new \Pimvc\View();
        $this->view->setFilename($this->partialFilename());
        $this->view->setParams($this->params());
        return $this;
    }

    /**
     * render
     *
     */
    public function render()
    {
        $this->view->render();
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
    private function params()
    {
        return [
            'mapHeight' => self::DEFAULT_HEIGHT
            , 'baseUrl' => $this->baseUrl
            , 'options' => $this->mapOptions
            , 'markers' => $this->markers
            , 'markersJson' => $this->getJsonMarkers()
            , 'baseUrl' => $this->baseUrl
            , 'layer' => $this->layer
        ];
    }

    private function getJsonMarkers()
    {
        $jsonMarker = [];
        foreach ($this->markers as $marker) {
            $jsonMarker[] = $marker->get(false);
        }
        return \json_encode($jsonMarker, JSON_PRETTY_PRINT);
    }

    public function setLayer($layer)
    {
        $this->layer = $layer;
        $this->view->setParams($this->params());
    }

    /**
     * partialFilename
     *
     * @return string
     */
    private function partialFilename()
    {
        return __DIR__ . self::TEMPLATE_PATH . self::TEMPLATE_PARTIAL;
    }
}
