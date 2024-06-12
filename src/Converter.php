<?php
namespace Biigle\Modules\MetadataIfdo;

use Biigle\MediaType;
use Biigle\Services\MetadataParsing\VolumeMetadata;

class Converter
{
    private $acquisitionFormat = '';

    public function __construct($acquisitionFormat)
    {
        $this->acquisitionFormat = $acquisitionFormat;
    }

    public function convert($ifdo)
    {
        $data = new VolumeMetadata($this->mediaType());
        foreach ($ifdo->getImageSetItems() as $name => $items)
        {
            if ( ! is_array($items))
            {
                $items = [$items];
            }

            foreach ($items as $item)
            {
                $takenAt = $item['image-datetime'] ?? null;
                $params  = [
                    'name'             => $name,
                    'lat'              => $this->maybeCastToFloat($item['image-latitude'] ?? null),
                    'lng'              => $this->maybeCastToFloat($item['image-longitude'] ?? null),
                    'takenAt'          => $takenAt,
                    'area'             => $this->maybeCastToFloat($item['image-area-square-meter'] ?? null),
                    'distanceToGround' => $this->maybeCastToFloat($item['image-meters-above-ground'] ?? null),
                    'gpsAltitude'      => $this->maybeCastToFloat($item['image-altitude'] ?? null),
                    'yaw'              => $this->maybeCastToFloat($item['image-camera-yaw-degrees'] ?? null),
                ];

                if ( ! is_null($fileData = $data->getFile($name)) && ! is_null($takenAt))
                {
                    $fileData->addFrame(...$params);
                }
                else
                {
                    $class    = $this->metadataClass();
                    $fileData = new $class(...$params);
                    $data->addFile($fileData);
                }

            }
        }

        return $data;
    }

    /**
     * Cast the value to float if it is not null or an empty string.
     */
    protected function maybeCastToFloat(?string $value): ?float
    {
        return (is_null($value) || $value === '') ? null : floatval($value);
    }

    private function mediaType()
    {
        switch ($this->acquisitionFormat)
        {
        case 'photo':
            return MediaType::image();
        case 'video':
            return MediaType::video();
        }
    }

    private function metadataClass()
    {
        switch ($this->acquisitionFormat)
        {
        case 'photo':
            return 'Biigle\Services\MetadataParsing\ImageMetadata';
        case 'video':
            return 'Biigle\Services\MetadataParsing\VideoMetadata';
        }
    }

}
