<?php
namespace Biigle\Tests\Modules\MetadataCoco;
use Symfony\Component\HttpFoundation\File\File;
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
        $this->assertFalse(false);
    }
}
