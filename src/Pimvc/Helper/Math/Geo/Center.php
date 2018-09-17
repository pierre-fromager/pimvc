<?php

/**
 * Pimvc\Helper\Math\Geo\Center
 *
 * get center as lat lng from a lat lng collection
 */
namespace Pimvc\Helper\Math\Geo;

class Center
{

    const HALF_CIRCLE = 180;

    /**
     * getFromAzimuts
     *
     * @param array $data as [...[$lat,$lon]]
     * @return array | false
     */
    public static function getFromAzimuts(array $data): array
    {
        if (!is_array($data)) {
            return false;
        }

        $num_coords = count($data);

        $x = 0.0;
        $y = 0.0;
        $z = 0.0;

        foreach ($data as $coord) {
            $lat = $coord[0] * pi() / self::HALF_CIRCLE;
            $lon = $coord[1] * pi() / self::HALF_CIRCLE;

            $a = cos($lat) * cos($lon);
            $b = cos($lat) * sin($lon);
            $c = sin($lat);

            $x += $a;
            $y += $b;
            $z += $c;
        }

        $x /= $num_coords;
        $y /= $num_coords;
        $z /= $num_coords;

        $lon = atan2($y, $x);
        $hyp = sqrt($x * $x + $y * $y);
        $lat = atan2($z, $hyp);

        return [self::radToDeg($lat), self::radToDeg($lon)];
    }

    /**
     * radToDeg
     *
     * @param type $rad
     * @return float
     */
    private static function radToDeg($rad): float
    {
        return $rad * self::HALF_CIRCLE / pi();
    }
}
