<?php

namespace Biigle\Modules\MetadataCoco;

class Info
{
    public ?int $year;
    public ?string $version;
    public ?string $description;
    public ?string $contributor;
    public ?string $url;
    public ?string $date_created;

    // Static create method from JSON
    public static function create(array $data): self
    {
        $instance = new self();
        $instance->year = $data['year'] ?? null;
        $instance->version = $data['version'] ?? null;
        $instance->description = $data['description'] ?? null;
        $instance->contributor = $data['contributor'] ?? null;
        $instance->url = $data['url'] ?? null;
        $instance->date_created = $data['date_created'] ?? null;

        return $instance;
    }
}
