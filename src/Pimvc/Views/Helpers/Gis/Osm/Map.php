<?php
/**
 * Description of Pimvc\Views\Helpers\Gis\Osm\Map
 *
 * @author pierrefromager
 */
namespace Pimvc\Views\Helpers\Gis\Osm;

use Marker;
use Pimvc\Views\Helpers\Gis\Osm\Options as MapOptions;

class Map
{

    //const GMAP_ICON_PIN = '/public/img/gmap/gmap_pin_orange.png';
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

    /**
     * __construct
     *
     * @param type $baseUrl
     * @param Marker[] $markers
     * @return $this
     */
    public function __construct(string $baseUrl, array $markers, MapOptions $mapOptions)
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
    public function __toString(): string
    {
        return (string) $this->view;
    }

    /**
     * options
     *
     * @return MapOptions
     */
    public function options(): MapOptions
    {
        return $this->mapOptions;
    }

    /**
     * params
     *
     * @return array
     */
    private function params(): array
    {
        return [
            'mapHeight' => self::DEFAULT_HEIGHT
            , 'baseUrl' => $this->baseUrl
            , 'options' => $this->mapOptions
            , 'markers' => $this->markers
            , 'baseUrl' => $this->baseUrl
        ];
    }

    /**
     * partialFilename
     *
     * @return string
     */
    private function partialFilename(): string
    {
        return __DIR__ . self::TEMPLATE_PATH . self::TEMPLATE_PARTIAL;
    }
}
