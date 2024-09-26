<?php
namespace Biigle\Modules\MetadataCoco;

use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;

class CocoParser extends MetadataParser
{
    /**
     * {@inheritdoc}
     */
    public static function getKnownMimeTypes(): array {
        return [
            'application/json',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return "COCO";
    }

    /**
     * {@inheritdoc}
     */
    public function recognizesFile(): bool
    {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): VolumeMetadata
    {
        // TODO
    }
}
