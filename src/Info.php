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

    public static function create(array $data): self
    {
        $instance = new self();
        $instance->year = self::parseYear($data['year'] ?? null);
        $instance->version = $data['version'] ?? null;
        $instance->description = $data['description'] ?? null;
        $instance->contributor = $data['contributor'] ?? null;
        $instance->url = $data['url'] ?? null;
        $instance->date_created = $data['date_created'] ?? null;

        return $instance;
    }

    /**
     * Parse year value to int or null, handling both string and int inputs
     */
    private static function parseYear($year): ?int
    {
        if ($year === null) {
            return null;
        }

        if (is_int($year)) {
            return $year;
        }

        if (is_numeric($year)) {
            return (int) $year;
        }

        return null;
    }
}
