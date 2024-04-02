<?php

namespace Biigle\Modules\MetadataIfdo;

use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;

class ImageIfdoParser extends MetadataParser
{
    /**
     * {@inheritdoc}
     */
    public static function getKnownMimeTypes(): array
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function recognizesFile(): bool
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): VolumeMetadata
    {
        //
    }
}
