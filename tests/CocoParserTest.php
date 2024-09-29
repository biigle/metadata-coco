<?php

namespace Biigle\Tests\Modules\MetadataCoco;

use Symfony\Component\HttpFoundation\File\File;
use Biigle\MediaType;
use Biigle\Shape;
use Biigle\Modules\MetadataCoco\Annotation;
use Biigle\Modules\MetadataCoco\CocoParser;

use TestCase;

class CocoParserTest extends TestCase
{
    public function testRecognizesCorrectFile()
    {
        $file   = new File(__DIR__ . "/files/full-coco-import-volume.json");
        $parser = new CocoParser($file);
        $this->assertTrue($parser->recognizesFile());
    }

    public function testRecognizesCorrectFileWithAdditionalData()
    {
        $file   = new File(__DIR__ . "/files/additional-data-coco-import-volume.json");
        $parser = new CocoParser($file);
        $this->assertTrue($parser->recognizesFile());
    }

    public function testRecognizesCorrectFileWithLicense()
    {
        $file   = new File(__DIR__ . "/files/license-coco-import-volume.json");
        $parser = new CocoParser($file);
        $this->assertTrue($parser->recognizesFile());
    }

    public function testRecognizesMissingCategoryFile()
    {
        $file   = new File(__DIR__ . "/files/missing-category-coco-import-volume.json");
        $parser = new CocoParser($file);
        $this->assertFalse($parser->recognizesFile());
    }

    public function testRecognizesEmptyFile()
    {
        $file   = new File(__DIR__ . "/files/empty.json");
        $parser = new CocoParser($file);
        $this->assertFalse($parser->recognizesFile());
    }

    public function testRecognizesBrokenLicenseFile()
    {
        $file   = new File(__DIR__ . "/files/broken-license-coco-import-volume.json");
        $parser = new CocoParser($file);
        $this->assertFalse($parser->recognizesFile());
    }

    public function testGetMetadata()
    {
        $file   = new File(__DIR__ . "/files/full-coco-import-volume.json");
        $parser = new CocoParser($file);
        $metadata = $parser->getMetadata();

        $this->assertSame(MediaType::imageId(), $metadata->type->id);
        $this->assertNull($metadata->name);
        $this->assertNull($metadata->url);
        $this->assertNull($metadata->handle);

        $this->assertCount(1, $metadata->getFiles());

        $file = $metadata->getFiles()->last();
        $this->assertSame("31c3-Wimmelbild-ccby.jpg", $file->name);
        $this->assertSame(null, $file->lng);
        $this->assertSame(null, $file->lat);

        $this->assertTrue($metadata->hasAnnotations());

        $annotations = $file->getAnnotations();
        $this->assertCount(4, $annotations);
    }

    public function testIsPointShape()
    {
        $pointAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [1, 1]
        ]);
        $this->assertTrue($pointAnnotation->isPointShape());
        $this->assertSame($pointAnnotation->getShape(), Shape::point());
    }

    public function testIsRectangleShape()
    {
        $rectangleAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [
                1853.22,
                596.22,
                
                1776.16,
                799.04,
                
                1597.21,
                731.04,

                1674.27,
                528.23
            ],
        ]);
        $this->assertTrue($rectangleAnnotation->isRectangleShape());
        $this->assertSame($rectangleAnnotation->getShape(), Shape::rectangle());
    }

    // public function testIsCircleShape()
    // {
    //     $circleAnnotation = Annotation::create([
    //         'id' => 1,
    //         'image_id' => 1,
    //         'category_id' => 1,
    //         'bbox' => null,
    //         'segmentation' => [1, 1, 2, 2, 3, 3]
    //     ]);
    //     $this->assertSame($circleAnnotation->getShape(), Shape::circle());
    // }

    public function testIsLineShape()
    {
        $lineAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [1, 1, 2, 2, 3, 3, 2, 2]
        ]);
        $this->assertSame($lineAnnotation->getShape(), Shape::line());
    }

    public function testIsPolygonShape()
    {
        $polygonAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [1, 1, 2, 2, 3, 3, 4, 4, 1, 1]
        ]);
        $this->assertSame($polygonAnnotation->getShape(), Shape::polygon());
    }
}
