<?php

namespace Biigle\Modules\MetadataCoco;

class Info
{
    public int $year;
    public string $version;
    public string $description;
    public string $contributor;
    public string $url;
    public string $date_created;

    // Static create method from JSON
    public static function create(array $data): self
    {
        self::validate($data);
        $instance = new self();
        $instance->year = $data['year'];
        $instance->version = $data['version'];
        $instance->description = $data['description'];
        $instance->contributor = $data['contributor'];
        $instance->url = $data['url'];
        $instance->date_created = new \string($data['date_created']);

        return $instance;
    }

    // Validate the structure
    public static function validate(array $data): void
    {
        $requiredKeys = ['year', 'version', 'description', 'contributor', 'url', 'date_created'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Missing key '$key' in Info");
            }
        }
    }
}
