<?php
namespace Biigle\Tests\Modules\MetadataCoco;
use Symfony\Component\HttpFoundation\File\File;
use Biigle\Modules\MetadataCoco\CocoParser;
use Biigle\MediaType;

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
}
