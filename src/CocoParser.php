<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\MediaType;
use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;
use Biigle\Services\MetadataParsing\ImageAnnotation;
use Biigle\Services\MetadataParsing\ImageMetadata;
use Biigle\Modules\MetadataCoco\Coco;
use Biigle\Modules\MetadataCoco\Image;

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

        foreach ($coco->images as $image) {
            $imageMetaData = new ImageMetadata(
                name: $image->file_name
            );

            $this->processImageAnnotations($image, $imageMetaData);

            $metadata->addFile($imageMetaData);
        }

        return $metadata;
    }

    private function processImageAnnotations(Image $image, ImageMetadata $imageMetaData)
    {
        $annotations = array_filter($this->getCoco()->annotations, function ($annotation) use ($image) {
            return $annotation->image_id === $image->id;
        });

        foreach ($annotations as $annotation) {
            $metaDataAnnotation = new ImageAnnotation(
                shape: $annotation->getShape(),
                points: $annotation->getPoints(),
                labels: $annotation->getLabelAndUsers($this->getCoco()->categories),
            );
            $imageMetaData->addAnnotation($metaDataAnnotation);
        }
    }
}
