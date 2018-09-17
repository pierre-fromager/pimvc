<?php
/**
 * find least number of hops (edges) between 2 nodes (vertices)
 */
namespace Pimvc\Helper\Math\Graph\Pathes;

class Minpath
{
    protected $graph;
    protected $visited = [];

    public function __construct($graph)
    {
        $this->graph = $graph;
    }

    /**
     * path
     *
     * @param string $origin
     * @param string $destination
     */
    public function path($origin, $destination)
    {
        // check exists origin destination
        if (!isset($this->graph[$origin]) || !isset($this->graph[$destination])) {
            return [];
        }
        // mark all nodes as unvisited
        foreach ($this->graph as $vertex => $adj) {
            $this->visited[$vertex] = false;
        }

        // create an empty queue
        $q = new \SplQueue();

        // enqueue the origin vertex and mark as visited
        $q->enqueue($origin);
        $this->visited[$origin] = true;

        // this is used to track the path back from each node
        $path = [];
        $path[$origin] = new \SplDoublyLinkedList();
        $path[$origin]->setIteratorMode(
            \SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_KEEP
        );

        $path[$origin]->push($origin);

        // while queue is not empty and destination not found
        while (!$q->isEmpty() && $q->bottom() != $destination) {
            $t = $q->dequeue();

            if (!empty($this->graph[$t])) {
                // for each adjacent neighbor
                foreach ($this->graph[$t] as $vertex) {
                    if (!$this->visited[$vertex]) {
                        // if not yet visited, enqueue vertex and mark
                        // as visited
                        $q->enqueue($vertex);
                        $this->visited[$vertex] = true;
                        // add vertex to current path
                        $path[$vertex] = clone $path[$t];
                        $path[$vertex]->push($vertex);
                    }
                }
            }
        }
        return (isset($path[$destination])) ? iterator_to_array($path[$destination]) : [];
    }
}
