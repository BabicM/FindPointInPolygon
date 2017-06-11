<?php

final class Polygon
{
    /** @var array $vertices */
    private $vertices = [];

    /** @var int $verticesCount */
    private $verticesCount = 0;

    /** @var bool $pointOnVertex */
    private $pointOnVertex = true;

    /** @var int $intersections */
    private $intersections = 0;

    /**
     * Load the polygon points and set whether boundary points are returned as a part of polygon.
     *
     * PolygonRepository constructor.
     * @param array $polygon
     * @param bool $pointOnEdge
     */
    public function __construct(array $polygon, $pointOnEdge = true)
    {
        $this->pointOnVertex = $pointOnEdge;
        $this->mapPolygonToVertices($polygon);
    }

    /**
     * Find point by coordinates.
     *
     * @param array $point
     * @return boolean
     */
    public function isPointInPolygon(array $point)
    {
        $this->intersections = 0;
        $point = $this->formatPoint($point);

        if ($this->pointOnVertex === true && $this->pointOnVertex($point) === true) {
            return true;
        }

        for ($i = 1; $i < $this->verticesCount; $i++) {
            $vertex1 = $this->vertices[$i - 1];
            $vertex2 = $this->vertices[$i];

            // Check if point is on an horizontal polygon boundary
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and
                $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])
            ) {
                return $this->pointOnVertex === true ? true : false;
            }
            if ($point['y'] >  min($vertex1['y'], $vertex2['y']) and
                $point['y'] <= max($vertex1['y'], $vertex2['y']) and
                $point['x'] <= max($vertex1['x'], $vertex2['x']) and
                $vertex1['y'] != $vertex2['y']
            ) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) /
                    ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];

                // Check if point is on the polygon boundary (other than horizontal)
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return $this->pointOnVertex === true ? true : false;
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $this->intersections++;
                }
            }
        }

        if ($this->intersections % 2 != 0) {
            return true;
        }

        return false;
    }

    /**
     * Format point from indexed array to associative array.
     *
     * @param array $point
     * @return array
     */
    private function formatPoint(array $point): array
    {
        return [
            'x' => (float) $point[0],
            'y' => (float) $point[1]
        ];
    }

    /**
     * Set vertices from polygon data, format them to associative array and check count of vertices.
     *
     * @param array $polygon
     */
    private function mapPolygonToVertices(array $polygon)
    {
        foreach ($polygon as $points) {
            $this->vertices[] = $this->formatPoint($points);
        }

        $this->verticesCount = count($this->vertices);
    }

    /**
     * Find out if the point you are looking for is not on the vertex.
     *
     * @param $point
     * @return bool
     */
    private function pointOnVertex($point)
    {
        foreach ($this->vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
        return false;
    }
}
