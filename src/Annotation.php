<?php

namespace Biigle\Modules\MetadataCoco;

class Annotation {
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
}