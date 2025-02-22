<?php

namespace Biigle\Modules\MetadataCoco;

class Category
{
    public int $id;
    public string $name;

    // Static create method from JSON
    public static function create(array $data): self
    {
        self::validate($data);
        $instance = new self();
        $instance->id = $data['id'];
        $instance->name = $data['name'];

        return $instance;
    }

    // Validate the structure
    public static function validate(array $data): void
    {
        $requiredKeys = ['id', 'name'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Missing key '$key' in Category");
            }

            if (is_null($data[$key])) {
                throw new \Exception("Missing value for '$key' in Category");
            }
        }
    }
}
