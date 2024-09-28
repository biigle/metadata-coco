<?php

namespace Biigle\Modules\MetadataCoco;

class Coco
{
    public ?Info $info;
    public array $images;
    public array $annotations;
    public ?array $licenses;
    public array $categories;

    public static function createFromPath(string $path): self
    {
        $data = file_get_contents($path);
        $data = json_decode($data, true);
        return self::create($data);
    }

    // Static create method from JSON
    public static function create(array $data): self
    {
        $instance = new self();

        // Validate top-level structure
        self::validate($data);

        // Create the Info object
        if (array_key_exists('info', $data)) {
            $instance->info = Info::create($data['info']);
        }

        // Create the Image objects
        $instance->images = array_map(function ($imageData) {
            return Image::create($imageData);
        }, $data['images']);

        // Handle annotations (assuming no specific validation required)
        $instance->annotations = $data['annotations'];


        // Create the Category objects
        $instance->categories = array_map(function ($categoryData) {
            return Category::create($categoryData);
        }, $data['categories']);

        // Create the License objects
        if (array_key_exists('licenses', $data)) {
            $instance->licenses = array_map(function ($licenseData) {
                return License::create($licenseData);
            }, $data['licenses']);
        }
        $instance->validateLicensesInData();
        return $instance;
    }

    // Validate the top-level structure
    public static function validate(array $data): void
    {
        $requiredKeys = ['images', 'annotations', 'categories'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Missing key '$key' in Coco");
            }
        }
    }

    /**
     * Validate that all image license IDs are valid.
     */
    public function validateLicensesInData(): void
    {
        if (!isset($this->licenses)) {
            return;
        }

        $licenseIds = array_map(function ($license) {
            return $license->id;
        }, $this->licenses);

        foreach ($this->images as $image) {
            if (isset($image->license) && !in_array($image->license, $licenseIds)) {
                throw new \Exception("Invalid license ID '{$image->license}' in image '{$image->id}'");
            }
        }
    }
}
