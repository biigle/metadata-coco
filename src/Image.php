<?php

namespace Biigle\Modules\MetadataCoco;

class Image
{
    public int $id;
    public int $width;
    public int $height;
    public string $file_name;
    public ?int $license = null;
    public ?string $flickr_url;
    public ?string $coco_url;

    public ?string $date_captured;

    // Static create method from JSON
    public static function create(array $data): self
    {
        self::validate($data);
        $instance = new self();
        $instance->id = $data['id'];
        $instance->width = $data['width'];
        $instance->height = $data['height'];
        $instance->file_name = $data['file_name'];
        $instance->license = $data['license'] ?? null;
        $instance->flickr_url = $data['flickr_url'] ?? null;
        $instance->coco_url = $data['coco_url'] ?? null;
        $instance->date_captured = $data['date_captured'] ?? null;

        return $instance;
    }

    // Validate the structure
    public static function validate(array $data): void
    {
        $requiredKeys = ['id', 'width', 'height', 'file_name'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Missing key '$key' in Image");
            }
        }
    }
}
