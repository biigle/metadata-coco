<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\Shape;
use Biigle\Label as LabelModel;
use Biigle\Services\MetadataParsing\Label;
use Biigle\Services\MetadataParsing\LabelAndUser;

class Annotation
{
    public int $id;
    public int $image_id;
    public int $category_id;
    public ?array $bbox;
    public array $segmentation;

    private ?Shape $shape = null;
    private ?array $points = null;
    private ?array $groupedPoints = null;

    public static function create(array $data): self
    {
        self::validate($data);
        $instance = new self();
        $instance->id = $data['id'];
        $instance->image_id = $data['image_id'];
        $instance->category_id = $data['category_id'];
        $instance->bbox = $data['bbox'] ?? null;
        $instance->segmentation = $data['segmentation'][0] ?? null;

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

    public function getLabel(array $categories): Label
    {
        $categoryIndex = array_search($this->category_id, array_column($categories, 'id'));
        $category = $categories[$categoryIndex];
        return new Label(id: $category->id, name: $category->name);
    }

    public function getLabelAndUsers(array $categories): array
    {
        $cocoUser = Coco::getCocoUser();
        $label = $this->getLabel($categories);
        return [new LabelAndUser(label: $label, user: $cocoUser)];
    }

    public function getPoints(): array
    {
        if ($this->isCircleShape()) {
            return $this->getCirclePoints();
        }
        return $this->segmentation;
    }

    private function getGroupedPoints(): array
    {
        if ($this->groupedPoints) {
            return $this->groupedPoints;
        }
        $groupedPoints = [];
        for ($i = 0; $i < count($this->segmentation); $i += 2) {
            $groupedPoints[] = ['x' => $this->segmentation[$i], 'y' => $this->segmentation[$i + 1]];
        }
        $this->groupedPoints = $groupedPoints;
        return $groupedPoints;
    }

    private function getCirclePoints()
    {
        if($this->points) {
            return $this->points;
        }
        // Split the coordinates into x, y pairs
        $points = $this->getGroupedPoints();

        // Calculate the average center (geometric center) of the points
        $maxY = max(array_column($points, 'y'));
        $minY = min(array_column($points, 'y'));
        $maxX = max(array_column($points, 'x'));
        $minX = min(array_column($points, 'x'));
        $centerX = ($maxX + $minX) / 2;
        $centerY = ($maxY + $minY) / 2;

        // Calculate the distance from the first point to the center (radius)
        $initialRadius = $this->euclidean_distance($points[0]['x'], $points[0]['y'], $centerX, $centerY);

        $this->points = [$centerX, $centerY, $initialRadius];
        return $this->points;
    }

    private function detectShape(): Shape
    {
        if (count($this->segmentation) < 2) {
            return Shape::polygon();
        }

        if ($this->isPointShape()) {
            return Shape::point();
        }

        if ($this->isRectangleShape()) {
            return Shape::rectangle();
        }

        if ($this->isLineShape()) {
            return Shape::line();
        }

        if ($this->isCircleShape()) {
            return Shape::circle();
        }

        return Shape::polygon();
    }

    public function getShape(): Shape
    {
        if (!$this->shape) {
            $this->shape = $this->detectShape();
        }

        return $this->shape;
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
        // Tolerance for floating-point comparison
        $tolerance = 0.001;
        $points = $this->getGroupedPoints();
        list($centerX, $centerY, $initialRadius) = $this->getCirclePoints();

        // Check if all points are equidistant from the center
        foreach ($points as $point) {
            $radius = $this->euclidean_distance($point['x'], $point['y'], $centerX, $centerY);
            if (abs($radius - $initialRadius) > $tolerance) {
                return false;
            }
        }

        // If all points are within the tolerance range, they form a circle
        return true;
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
