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

        // If it's already an int, return as-is
        if (is_int($year)) {
            return $year;
        }

        // If it's a numeric string (including floats), convert to int
        if (is_numeric($year)) {
            return (int) $year;
        }

        // For non-numeric strings (including empty string), return null
        return null;
    }
}
