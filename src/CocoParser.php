<?php

namespace Biigle\Modules\MetadataCoco;

use Biigle\MediaType;
use Biigle\Shape;
use Biigle\Services\MetadataParsing\MetadataParser;
use Biigle\Services\MetadataParsing\VolumeMetadata;
use Biigle\Services\MetadataParsing\FileMetadata;
use Biigle\Services\MetadataParsing\Annotation;
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
            url: $coco->info->url ?? null,
            handle: $coco->info->version ?? null,
        );

        foreach ($coco->images as $image) {
            $fileMetaData = new FileMetadata(
                name: $image->file_name
            );
            
            $this->processAnnotations($image, $fileMetaData);

            $metadata->addFile($fileMetaData);
        }
    
        return $metadata;
    }

    private function processAnnotations(Image $image, FileMetaData $fileMetaData)
    {
        $annotations = array_filter($this->getCoco()->annotations, function ($annotation) use ($image) {
            return $annotation->image_id === $image->id;
        });

        foreach ($annotations as $annotation) {
            $annotation = new Annotation(
                shape: $this->get_shape($annotation),
                points: $annotation->segmentation,
                labels: $this->get_labels($annotation),
            );
            $fileMetaData->addAnnotation($annotation);
        }
    }

    private function get_labels(MetadataAnnotation $annotation): array
    {
        $category = array_filter($this->getCoco()->categories, function ($category) use ($annotation) {
            return $category->id === $annotation->category_id;
        })[0];
        return [$category->name];
    }

    public function get_shape(MetadataAnnotation $annotation): Shape
    {
        return Shape::polygon();
    }
}
