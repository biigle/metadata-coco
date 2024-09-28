<?php

namespace Biigle\Modules\MetadataCoco;

class License
{
    public int $id;
    public string $name;
    public string $url;

    // Static create method from JSON
    public static function create(array $data): self
    {
        self::validate($data);
        $instance = new self();
        $instance->id = $data['id'];
        $instance->name = $data['name'];
        $instance->url = $data['url'];

        return $instance;
    }

    // Validate the structure
    public static function validate(array $data): void
    {
        $requiredKeys = ['id', 'name', 'url'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Missing key '$key' in License");
            }
        }
    }
}
