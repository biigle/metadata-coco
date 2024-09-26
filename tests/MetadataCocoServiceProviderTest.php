<?php
namespace Biigle\Tests\Modules\MetadataCoco;

use Biigle\Modules\MetadataCoco\CocoParser;
use Biigle\Modules\MetadataCoco\MetadataCocoServiceProvider;
use Biigle\Services\MetadataParsing\ParserFactory;
use TestCase;

class MetadataCocoServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $this->assertTrue(class_exists(MetadataCocoServiceProvider::class));
    }

    public function testGetImage()
    {
        $this->assertTrue(ParserFactory::has('image', CocoParser::class));
    }

    public function testGetVideo()
    {
        $this->assertFalse(ParserFactory::has('video', CocoParser::class));
    }
}
