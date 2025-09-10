<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\MediaType;
use Biigle\Modules\MetadataCoco\Coco;
use Biigle\Modules\MetadataCoco\Image;
use Biigle\Services\MetadataParsing\ImageAnnotation;
use Biigle\Services\MetadataParsing\ImageMetadata;
use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;
use Illuminate\Support\Collection;

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
            // Sometimes JSON file MIME types are not recognized and reported as
            // text/plain instead. If this is no JSON file, it will be caught by
            // recognizesFile() later.
            'text/plain',
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
        $coco = $this->getCoco();

        $metadata = new VolumeMetadata(
            type: MediaType::image(),
            name: $coco->info->description ?? null,
            url: null,
            handle: null,
        );

        $annotations = collect($coco->annotations)->groupBy('image_id');
        $categories = $coco->categories;

        foreach ($coco->images as $image) {
            $imageMetaData = new ImageMetadata(
                name: $image->file_name
            );

            if ($annotations->has($image->id)) {
                $imageMetaData->annotations = $this->processImageAnnotations($annotations->get($image->id), $categories);
            }

            $metadata->addFile($imageMetaData);
        }

        return $metadata;
    }

    private function processImageAnnotations(Collection $annotations, array $categories)
    {
        return $annotations->map(function ($annotation) use ($categories) {
            return new ImageAnnotation(
                shape: $annotation->getShape(),
                points: $annotation->getPoints(),
                labels: $annotation->getLabelAndUsers($categories),
            );
        })->toArray();
    }
}
