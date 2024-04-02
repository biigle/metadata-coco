<?php

namespace Biigle\Tests\Modules\MetadataIfdo;

use Biigle\Modules\MetadataIfdo\ImageIfdoParser;
use Biigle\Modules\MetadataIfdo\MetadataIfdoServiceProvider;
use Biigle\Modules\MetadataIfdo\VideoIfdoParser;
use Biigle\Services\MetadataParsing\ParserFactory;
use TestCase;

class MetadataIfdoServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $this->assertTrue(class_exists(MetadataIfdoServiceProvider::class));
    }

    public function testGetImageIfdo()
    {
        $this->markTestIncomplete('implement metadata parser');

        $file = new File(__DIR__."/files/image-ifdo.json");
        $parser = ParserFactory::getParserForFile($file, 'image');
        $this->assertInstanceOf(ImageIfdoParser::class, $parser);
    }

    public function testGetVideoIfdo()
    {
        $this->markTestIncomplete('implement metadata parser');

        $file = new File(__DIR__."/files/video-ifdo.json");
        $parser = ParserFactory::getParserForFile($file, 'video');
        $this->assertInstanceOf(VideoIfdoParser::class, $parser);
    }
}
