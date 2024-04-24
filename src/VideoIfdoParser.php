<?php

namespace Biigle\Modules\MetadataIfdo;

use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;

class VideoIfdoParser extends MetadataParser
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
    public static function getName(): string
    {
        return 'iFDO';
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
