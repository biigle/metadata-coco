<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\Shape;

class Annotation
{
    public int $id;
    public int $image_id;
    public int $category_id;
    public ?array $bbox;
    public array $segmentation;

    // Static create method from JSON
    public static function create(array $data): self
    {
        self::validate($data);
        $instance = new self();
        $instance->id = $data['id'];
        $instance->image_id = $data['image_id'];
        $instance->category_id = $data['category_id'];
        $instance->bbox = $data['bbox'] ?? null;
        $instance->segmentation = $data['segmentation'] ?? null;

        return $instance;
    }

    // Validate the structure
    public static function validate(array $data): void
    {
        $requiredKeys = ['id', 'image_id', 'category_id'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Missing key '$key' in Annotation");
            }
        }
    }

    public function getShape(): Shape
    {
        if ($this->isPointShape()) {
            return Shape::point();
        }

        if ($this->isRectangleShape()) {
            return Shape::rectangle();
        }

        if ($this->isCircleShape()) {
            return Shape::circle();
        }

        if ($this->isLineShape()) {
            return Shape::line();
        }

        return Shape::polygon();
    }

    public function isPointShape(): bool
    {
        return count($this->segmentation) === 2;
    }

    public function isLineShape(): bool
    {
        $segmentationCount = count($this->segmentation);
        if ($segmentationCount < 4) {
            return false;
        }

        $x_1 = $this->segmentation[0];
        $y_1 = $this->segmentation[1];
        $x_last = $this->segmentation[$segmentationCount - 2];
        $y_last = $this->segmentation[$segmentationCount - 1];

        return !($x_1 === $x_last && $y_1 === $y_last);
    }

    public function isCircleShape(): bool
    {
        return false;
    }

    function euclidean_distance($x1, $y1, $x2, $y2)
    {
        return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    }

    public function isRectangleShape(): bool
    {
        if (count($this->segmentation) !== 8) {
            return false;
        }

        // Toleranz für Gleitkomma-Vergleiche
        $tolerance = 0.01;

        // Punkte (x1, y1), (x2, y2), (x3, y3), (x4, y4)
        list($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) = $this->segmentation;

        // Berechne die Seitenlängen
        $d1 = $this->euclidean_distance($x1, $y1, $x2, $y2); // Distanz zwischen P1 und P2
        $d2 = $this->euclidean_distance($x2, $y2, $x3, $y3); // Distanz zwischen P2 und P3
        $d3 = $this->euclidean_distance($x3, $y3, $x4, $y4); // Distanz zwischen P3 und P4
        $d4 = $this->euclidean_distance($x4, $y4, $x1, $y1); // Distanz zwischen P4 und P1

        // Berechne die Diagonalen
        $diag1 = $this->euclidean_distance($x1, $y1, $x3, $y3); // Diagonale P1 -> P3
        $diag2 = $this->euclidean_distance($x2, $y2, $x4, $y4); // Diagonale P2 -> P4

        // Prüfen, ob gegenüberliegende Seiten gleich lang sind und Diagonalen gleich lang sind
        return abs($d1 - $d3) < $tolerance && abs($d2 - $d4) < $tolerance && abs($diag1 - $diag2) < $tolerance;
    }
}
