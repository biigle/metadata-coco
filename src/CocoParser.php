<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;
use Biigle\Modules\MetadataCoco\Coco;

class CocoParser extends MetadataParser
{
    private $coco = null;

    /**
     * {@inheritdoc}
     */
    public static function getKnownMimeTypes(): array
    {
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

    private function getCoco(): Coco
    {
        if (!$this->coco) {
            $file       = parent::getFileObject();
            $this->coco = Coco::createFromPath($file->getRealPath());
        }

        return $this->coco;
    }

    /**
     * {@inheritdoc}
     */
    public function recognizesFile(): bool
    {
        try {
            $this->getCoco();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): VolumeMetadata
    {
        // TODO
    }
}
