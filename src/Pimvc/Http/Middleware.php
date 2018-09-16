<?php

namespace Pimvc\Http;

use InvalidArgumentException;
use Closure;
use Pimvc\Http\Interfaces\Layer as LayerInterface;

class Middleware
{
    private static $excMsg =  ' is not a valid onion layer.';
    private $layers;

    public function __construct(array $layers = [])
    {
        $this->layers = $layers;
    }

    /**
     * Add layer(s) or Middleware
     * @param  mixed $layers
     * @return Middleware
     */
    public function layer($layers)
    {
        if ($layers instanceof Middleware) {
            $layers = $layers->toArray();
        }

        if ($layers instanceof LayerInterface) {
            $layers = [$layers];
        }

        if (!is_array($layers)) {
            throw new InvalidArgumentException(
                get_class($layers) . self::$excMsg
            );
        }

        return new static(array_merge($this->layers, $layers));
    }

    /**
     * Run middleware around core function and pass an
     * object through it
     * @param  mixed  $object
     * @param  Closure $core
     * @return mixed
     */
    public function peel($object, \Closure $core)
    {
        $coreFunction = $this->createCoreFunction($core);
        $layers = array_reverse($this->layers);
        $completeMiddleware = array_reduce(
            $layers,
            function ($nextLayer, $layer) {
                return $this->createLayer($nextLayer, $layer);
            },
            $coreFunction
        );
        return $completeMiddleware($object);
    }

    /**
     * Get the layers of this onion, can be used to merge with another onion
     * @return array
     */
    public function toArray()
    {
        return $this->layers;
    }

    /**
     * The inner function of the onion.
     * This function will be wrapped on layers
     * @param  Closure $core the core function
     * @return Closure
     */
    private function createCoreFunction(\Closure $core)
    {
        return function ($object) use ($core) {
            return $core($object);
        };
    }

    /**
     * Get an onion layer function.
     * This function will get the object from a previous layer and pass it inwards
     * @param  LayerInterface $nextLayer
     * @param  LayerInterface $layer
     * @return Closure
     */
    private function createLayer($nextLayer, $layer)
    {
        return function ($object) use ($nextLayer, $layer) {
            return $layer->peel($object, $nextLayer);
        };
    }
}
