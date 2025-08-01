<?php

namespace Biigle\Tests\Modules\MetadataCoco;

use Biigle\Modules\MetadataCoco\Info;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    public function testCreateWithIntegerYear()
    {
        $info = Info::create(['year' => 2017]);
        $this->assertSame(2017, $info->year);
    }

    public function testCreateWithStringYear()
    {
        $info = Info::create(['year' => "2017"]);
        $this->assertSame(2017, $info->year);
    }

    public function testCreateWithFloatStringYear()
    {
        $info = Info::create(['year' => "2017.5"]);
        $this->assertSame(2017, $info->year);
    }

    public function testCreateWithEmptyStringYear()
    {
        $info = Info::create(['year' => ""]);
        $this->assertNull($info->year);
    }

    public function testCreateWithNonNumericStringYear()
    {
        $info = Info::create(['year' => "unknown"]);
        $this->assertNull($info->year);
    }

    public function testCreateWithNullYear()
    {
        $info = Info::create(['year' => null]);
        $this->assertNull($info->year);
    }

    public function testCreateWithoutYear()
    {
        $info = Info::create([]);
        $this->assertNull($info->year);
    }

    public function testCreateWithOtherFields()
    {
        $data = [
            'year' => "2023",
            'version' => "1.0",
            'description' => "Test COCO dataset",
            'contributor' => "Test Contributor",
            'url' => "https://example.com",
            'date_created' => "2023-01-01"
        ];

        $info = Info::create($data);

        $this->assertSame(2023, $info->year);
        $this->assertSame("1.0", $info->version);
        $this->assertSame("Test COCO dataset", $info->description);
        $this->assertSame("Test Contributor", $info->contributor);
        $this->assertSame("https://example.com", $info->url);
        $this->assertSame("2023-01-01", $info->date_created);
    }
}