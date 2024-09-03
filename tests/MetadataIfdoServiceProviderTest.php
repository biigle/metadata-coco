<?php
namespace Biigle\Tests\Modules\MetadataIfdo;

use Biigle\Modules\MetadataIfdo\IfdoParser;
use Biigle\Modules\MetadataIfdo\MetadataIfdoServiceProvider;
use Biigle\Modules\MetadataIfdo\VideoIfdoParser;
use Biigle\Services\MetadataParsing\ParserFactory;
use Symfony\Component\HttpFoundation\File\File;
use TestCase;

class MetadataIfdoServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $this->assertTrue(class_exists(MetadataIfdoServiceProvider::class));
    }

    public function testGetImageIfdo()
    {
        $this->assertTrue(ParserFactory::has('image', IfdoParser::class));
    }

    public function testGetVideoIfdo()
    {
        $this->assertTrue(ParserFactory::has('video', IfdoParser::class));
    }
}
