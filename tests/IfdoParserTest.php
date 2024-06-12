<?php
namespace Biigle\Tests\Modules\MetadataIfdo;

use Biigle\MediaType;
use Biigle\Modules\MetadataIfdo\IfdoParser;
use Symfony\Component\HttpFoundation\File\File;
use TestCase;

class ImageIfdoParserTest extends TestCase
{
    public function testGetMetadata()
    {
        $file   = new File(__DIR__ . "/files/image-ifdo.json");
        $parser = new IfdoParser($file);
        $data   = $parser->getMetadata();
        $this->assertEquals(MediaType::imageId(), $data->type->id);
        $this->assertNull($data->name);
        $this->assertNull($data->url);
        $this->assertNull($data->handle);
        $this->assertCount(2, $data->getFiles());
        $file = $data->getFiles()->last();
        $this->assertEquals('SO268-2_100-1_OFOS_SO_CAM-1_20190406_052726.JPG', $file->name);
        $this->assertEquals('2019-04-06 05:27:26.000000', $file->takenAt);
        $this->assertEquals(-117.0214286, $file->lng);
        $this->assertEquals(11.8582192, $file->lat);
        $this->assertEquals(-4129.6, $file->gpsAltitude);
        $this->assertEquals(2.1, $file->distanceToGround);
        $this->assertEquals(5.1, $file->area);
        $this->assertEquals(21, $file->yaw);
    }
}
