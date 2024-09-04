<?php
namespace Biigle\Modules\MetadataIfdo;

use Biigle\MediaType;
use Biigle\Services\MetadataParsing\Label;
use Biigle\Services\MetadataParsing\LabelAndUser;
use Biigle\Services\MetadataParsing\User;
use Biigle\Services\MetadataParsing\VolumeMetadata;
use Biigle\Shape;
use Illuminate\Support\Arr;

class Converter
{
    private $acquisitionFormat = '';
    private $annotators        = [];
    private $labels            = [];

    public function __construct($acquisitionFormat)
    {
        $this->acquisitionFormat = $acquisitionFormat;
    }

    public function convert($ifdo)
    {
        $header = $ifdo->getImageSetHeader();
        $this->annotators = $this->extractIfdoAnnotators($header['image-annotation-creators']);
        $this->labels     = $this->extractIfdoLabels($header['image-annotation-labels']);

        $data = new VolumeMetadata(
            type: $this->mediaType(),
            name: $header['image-set-name'] ?? null,
            handle: $this->parseHandle($header['image-set-handle'] ?? null),
        );

        foreach ($ifdo->getImageSetItems() as $name => $items)
        {
            if ( ! array_is_list($items))
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
                    $fileData->addFrame(...Arr::except($params, ['name']));
                }
                else
                {
                    $class    = $this->metadataClass();
                    $fileData = new $class(...$params);

                    if (isset($item['image-annotations']))
                    {
                        $this->addAnnotations($fileData, $item['image-annotations']);
                    }

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

    private function addAnnotations($fileData, $ifdoAnnotations)
    {
        foreach ($ifdoAnnotations as $ifdoAnnotation)
        {
            $fileData->addAnnotation($this->ifdoToBiigleAnnotation($ifdoAnnotation));
        }
    }

    private function ifdoToBiigleAnnotation($ifdoAnnotation)
    {
        $class  = $this->annotationClass();
        $params = [
            $this->ifdoShapeToBiigleShape($ifdoAnnotation['shape']),
            $this->ifdoParseCoordinates($ifdoAnnotation['coordinates']),
            $this->ifdoLabelsToBiigleLabelAndUsers($ifdoAnnotation['labels']),
        ];

        if (isset($ifdoAnnotation['frames']))
        {
            $params[] = $ifdoAnnotation['frames'];
        }

        $annotation = new $class(...$params);

        return $annotation;
    }

    private function ifdoLabelsToBiigleLabelAndUsers($ifdoLabels)
    {
        return array_map([$this, 'ifdoLabelToBiigleLabelAndUser'], $ifdoLabels);
    }

    private function ifdoLabelToBiigleLabelAndUser($ifdoLabel)
    {
        return new LabelAndUser(
            $this->labelForId($ifdoLabel['label']),
            $this->userForId($ifdoLabel['annotator'])
        );
    }

    private function extractIfdoLabels($ifdoLabels)
    {
        $labels = [];
        foreach ($ifdoLabels as $ifdoLabel)
        {
            $labels[$ifdoLabel['id']] = new Label(
                $ifdoLabel['id'],
                $ifdoLabel['name'],
                $ifdoLabel['color'] ?? null,
                $ifdoLabel['uuid'] ?? null,
            );
        }
        return $labels;
    }

    private function extractIfdoAnnotators($ifdoAnnotators)
    {
        $annotators = [];
        foreach ($ifdoAnnotators as $ifdoAnnotator)
        {
            $annotators[$ifdoAnnotator['id']] = new User(
                $ifdoAnnotator['id'],
                $ifdoAnnotator['name'],
                $ifdoAnnotator['uuid'] ?? null,
            );
        }
        return $annotators;
    }

    private function userForId($id)
    {
        return $this->annotators[$id];
    }

    private function labelForId($id)
    {
        return $this->labels[$id];
    }

    private function ifdoShapeToBiigleShape($shape)
    {
        switch ($shape)
        {
        case 'single-pixel':
            return Shape::point();
        case 'polyline':
            return Shape::line();
        case 'polygon':
            return Shape::polygon();
        case 'circle':
            return Shape::circle();
        case 'rectangle':
            return Shape::rectangle();
        case 'ellipse':
            return Shape::ellipse();
        case 'whole-frame':
            return Shape::wholeFrame();
        }
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

    private function annotationClass()
    {
        switch ($this->acquisitionFormat)
        {
        case 'photo':
            return 'Biigle\Services\MetadataParsing\ImageAnnotation';
        case 'video':
            return 'Biigle\Services\MetadataParsing\VideoAnnotation';
        }
    }

    private function ifdoParseCoordinates($coordinates)
    {
        if ($this->acquisitionFormat === 'photo') {
            return $coordinates[0];
        }

        return $coordinates;
    }

    private function parseHandle(?string $handle): ?string
    {
        return substr(parse_url($handle ?? '', PHP_URL_PATH) ?? '', 1) ?: null;
    }
}
