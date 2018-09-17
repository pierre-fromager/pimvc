<?php
/**
 * Dijikstra for weighted graph
 *
 */
namespace Pimvc\Helper\Math\Graph\Pathes;

class Weighted
{
    const _INF = 99999;

    protected $graph;
    private $src;
    private $dst;
    private $path = [];
    //the nearest path with its parent and weight
    private $s = [];
    //the left nodes without the nearest path
    private $q = [];

    /**
     * __construct
     *
     * @param array $graph
     */
    public function __construct($graph)
    {
        $this->graph = $graph;
    }

    /**
     * path
     *
     * @param string $src
     * @param string $dst
     * @return array
     */
    public function path($src, $dst)
    {
        $this->init($src, $dst);
        if (isset($this->graph[$this->src]) && isset($this->graph[$this->dst])) {
            $this->search();
            $this->processPath();
        } else {
            $this->path = [];
        }
        return $this->path;
    }

    /**
     * distance
     *
     * @return float
     */
    public function distance()
    {
        return ($this->path) ? $this->s[$this->dst][1] : 0;
    }

    /**
     * search
     *
     * start calculating
     */
    private function search()
    {
        while (!empty($this->q)) {
            $min = $this->min();
            if ($min == $this->dst) {
                break;
            }

            $keys = array_keys($this->graph[$min]);
            $kam = count($keys);
            for ($c = 0; $c < $kam; $c++) {
                $k = $keys[$c];
                $v = $this->graph[$min][$k];
                if (!empty($this->q[$k])) {
                    if (($checkMin = $this->q[$min] + $v) < $this->q[$k]) {
                        $this->q[$k] = $checkMin;
                        $this->s[$k] = [$min, $this->q[$k]];
                    }
                }
            }

            unset($this->q[$min]);
        }
    }

    /**
     * min
     *
     * the most min weight
     *
     * @return float
     */
    private function min()
    {
        return array_search(min($this->q), $this->q);
    }

    /**
     * init
     *
     * init queue assuming all edges are bi-directional
     */
    private function init($src, $dst)
    {
        $this->src = $src;
        $this->dst = $dst;
        $this->s = [];
        $this->q = [];
        foreach (array_keys($this->graph) as $v) {
            $this->q[$v] = self::_INF;
        }
        $this->q[$this->src] = 0;
    }

    /**
     * processPath
     *
     * list the path
     */
    private function processPath()
    {
        $this->path = [];
        $pos = $this->dst;
        while ($pos != $this->src) {
            $this->path[] = $pos;
            $pos = $this->s[$pos][0];
        }
        $this->path[] = $this->src;
        $this->path = array_reverse($this->path);
    }
}
