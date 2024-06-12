<?php
namespace Biigle\Modules\MetadataIfdo;

use Biigle\Ifdo\Ifdo;
use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;

class IfdoParser extends MetadataParser
{
    private $ifdo = null;

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
        return "iFDO";
    }

    public function getIfdo(): Ifdo
    {
        if ( ! $this->ifdo)
        {
            $file       = parent::getFileObject();
            $this->ifdo = Ifdo::fromFile($file->getRealPath());
        }

        return $this->ifdo;
    }

    /**
     * {@inheritdoc}
     */
    public function recognizesFile(): bool
    {
        return $this->getIfdo()->isValid();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): VolumeMetadata
    {
        $converter = new Converter($this->getIfdo()->getImageSetHeader()['image-acquisition']);
        return $converter->convert($this->getIfdo());
    }
}
