<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\MediaType;
use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;
use Biigle\Services\MetadataParsing\Annotation;
use Biigle\Services\MetadataParsing\ImageMetadata;
use Biigle\Modules\MetadataCoco\Coco;
use Biigle\Modules\MetadataCoco\Image;
use Biigle\Modules\MetadataCoco\Annotation as MetadataAnnotation;

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

            $this->processAnnotations($image, $imageMetaData);

            $metadata->addFile($imageMetaData);
        }

        return $metadata;
    }

    private function processAnnotations(Image $image, ImageMetadata $imageMetaData)
    {
        $annotations = array_filter($this->getCoco()->annotations, function ($annotation) use ($image) {
            return $annotation->image_id === $image->id;
        });

        foreach ($annotations as $annotation) {
            // LabelAndUser -> User ist der aktuelle User / fake
            $metaDataAnnotation = new Annotation(
                shape: $annotation->getShape(),
                points: $annotation->segmentation,
                labels: [],
            );
            $imageMetaData->addAnnotation($metaDataAnnotation);
        }
    }

    private function get_labels(MetadataAnnotation $annotation): array
    {
        $category = array_filter($this->getCoco()->categories, function ($category) use ($annotation) {
            return $category->id === $annotation->category_id;
        })[0];
        return [$category->name];
    }
}
