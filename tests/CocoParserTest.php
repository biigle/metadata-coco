<?php

namespace Biigle\Tests\Modules\MetadataCoco;

use Biigle\MediaType;
use Biigle\Modules\MetadataCoco\Annotation;
use Biigle\Modules\MetadataCoco\Coco;
use Biigle\Modules\MetadataCoco\CocoParser;
use Biigle\Modules\MetadataCoco\Info;
use Biigle\Shape;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
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

    public function testGetMetadata()
    {
        $file   = new File(__DIR__ . "/files/full-coco-import-volume.json");
        $parser = new CocoParser($file);
        $metadata = $parser->getMetadata();

        $this->assertSame(MediaType::imageId(), $metadata->type->id);
        $this->assertSame("COCO 2017 Volume", $metadata->name);
        $this->assertNull($metadata->url);
        $this->assertNull($metadata->handle);

        $this->assertCount(1, $metadata->getFiles());

        $file = $metadata->getFiles()->first();
        $this->assertSame("31c3-Wimmelbild-ccby.jpg", $file->name);
        $this->assertSame(null, $file->lng);
        $this->assertSame(null, $file->lat);

        $this->assertTrue($metadata->hasAnnotations());

        $annotations = $file->getAnnotations();
        $this->assertCount(6, $annotations);
        $this->assertSame(Shape::rectangle(), $annotations[0]->shape);
        $this->assertSame(Shape::circle(), $annotations[1]->shape);
        $this->assertSame(Shape::line(), $annotations[2]->shape);
        $this->assertSame(Shape::polygon(), $annotations[3]->shape);
        $this->assertSame(Shape::rectangle(), $annotations[4]->shape);
        $this->assertSame(Shape::polygon(), $annotations[5]->shape);

        $this->assertSame($annotations[0]->points, [
            1853.22,
            596.22,
            1776.16,
            799.04,
            1597.21,
            731.04,
            1674.27,
            528.23
        ]);

        $users = $file->getUsers();
        $cocoUser = Coco::getCocoUser();
        $this->assertCount(1, $users);
        $this->assertSame($cocoUser->name, $users[array_key_first($users)]->name);

        $labels = $file->getAnnotationLabels();
        $this->assertCount(1, $labels);
        $this->assertSame("Animal", $labels[array_key_first($labels)]->name);

        $this->assertSame("Animal", $annotations[0]->labels[0]->label->name);
        $this->assertSame("Animal", $annotations[1]->labels[0]->label->name);
        $this->assertSame("Animal", $annotations[2]->labels[0]->label->name);
        $this->assertSame("Animal", $annotations[3]->labels[0]->label->name);
        $this->assertSame("Animal", $annotations[4]->labels[0]->label->name);
        $this->assertSame("Animal", $annotations[5]->labels[0]->label->name);
    }

    public function testValidateSegmentation()
    {
        $this->expectException(Exception::class);
        Annotation::validate([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
        ]);
    }

    public function testUseSegmentationSingleArray()
    {
        $annotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [1, 1]
        ]);
        $this->assertSame([1, 1], $annotation->getPoints());
    }

    public function testIsPointShape()
    {
        $pointAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [[1, 1]]
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
                [
                    1853.22,
                    596.22,

                    1776.16,
                    799.04,

                    1597.21,
                    731.04,

                    1674.27,
                    528.23
                ]
            ],
        ]);
        $this->assertTrue($rectangleAnnotation->isRectangleShape());
        $this->assertSame($rectangleAnnotation->getShape(), Shape::rectangle());
        $this->assertSame($rectangleAnnotation->getPoints(), $rectangleAnnotation->segmentation);
    }

    public function testIsCircleShape()
    {
        $circleAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [[
                1474.3300000000002,
                1165.54,
                1473.8187624307873,
                1155.1335202112105,
                1472.289973220411,
                1144.8272605115476,
                1469.7583554446887,
                1134.7204758158937,
                1466.2482899667234,
                1124.9104999857984,
                1461.793580635865,
                1115.4918084511837,
                1456.4371287381614,
                1106.5551083603089,
                1450.2305198335218,
                1098.1864650203456,
                1443.2335269585758,
                1090.4664730414243,
                1435.5135349796544,
                1083.4694801664782,
                1427.1448916396912,
                1077.2628712618387,
                1418.2081915488163,
                1071.9064193641352,
                1408.7895000142016,
                1067.4517100332766,
                1398.9795241841064,
                1063.9416445553113,
                1388.8727394884525,
                1061.410026779589,
                1378.5664797887896,
                1059.8812375692128,
                1368.16,
                1059.37,
                1357.7535202112106,
                1059.8812375692128,
                1347.4472605115477,
                1061.410026779589,
                1337.3404758158938,
                1063.9416445553113,
                1327.5304999857985,
                1067.4517100332766,
                1318.1118084511838,
                1071.9064193641352,
                1309.175108360309,
                1077.2628712618387,
                1300.806465020346,
                1083.4694801664782,
                1293.0864730414244,
                1090.4664730414243,
                1286.0894801664783,
                1098.1864650203456,
                1279.8828712618388,
                1106.5551083603089,
                1274.5264193641353,
                1115.4918084511837,
                1270.0717100332768,
                1124.9104999857984,
                1266.5616445553114,
                1134.7204758158937,
                1264.0300267795892,
                1144.8272605115476,
                1262.501237569213,
                1155.1335202112105,
                1261.99,
                1165.54,
                1262.501237569213,
                1175.9464797887895,
                1264.0300267795892,
                1186.2527394884523,
                1266.5616445553114,
                1196.3595241841062,
                1270.0717100332768,
                1206.1695000142015,
                1274.526419364135,
                1215.5881915488162,
                1279.8828712618388,
                1224.524891639691,
                1286.0894801664783,
                1232.893534979654,
                1293.0864730414244,
                1240.6135269585757,
                1300.8064650203457,
                1247.6105198335217,
                1309.175108360309,
                1253.8171287381613,
                1318.1118084511838,
                1259.1735806358647,
                1327.5304999857985,
                1263.6282899667233,
                1337.3404758158938,
                1267.1383554446886,
                1347.4472605115477,
                1269.6699732204108,
                1357.7535202112106,
                1271.1987624307872,
                1368.16,
                1271.71,
                1378.5664797887894,
                1271.1987624307872,
                1388.8727394884525,
                1269.6699732204108,
                1398.9795241841064,
                1267.1383554446886,
                1408.7895000142016,
                1263.6282899667233,
                1418.2081915488163,
                1259.1735806358647,
                1427.1448916396912,
                1253.8171287381613,
                1435.5135349796544,
                1247.6105198335217,
                1443.2335269585758,
                1240.6135269585757,
                1450.2305198335218,
                1232.8935349796543,
                1456.4371287381614,
                1224.524891639691,
                1461.7935806358648,
                1215.5881915488162,
                1466.2482899667234,
                1206.1695000142015,
                1469.7583554446887,
                1196.3595241841062,
                1472.289973220411,
                1186.2527394884523,
                1473.8187624307873,
                1175.9464797887895,
                1474.3300000000002,
                1165.54
            ]]
        ]);
        $this->assertTrue($circleAnnotation->isCircleShape());
        $this->assertSame($circleAnnotation->getShape(), Shape::circle());
        $this->assertSame($circleAnnotation->getPoints(), [1368.16, 1165.54, 106.17000000000007]);
    }

    public function testIsLineShape()
    {
        $lineAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [[1, 1, 2, 2, 3, 3, 2, 2]]
        ]);
        $this->assertTrue($lineAnnotation->isLineShape());
        $this->assertSame($lineAnnotation->getShape(), Shape::line());
    }

    public function testIsPolygonShape()
    {
        $polygonAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => null,
            'segmentation' => [[1, 1, 2, 2, 3, 3, 4, 4, 1, 1]]
        ]);
        $this->assertSame($polygonAnnotation->getShape(), Shape::polygon());
    }

    public function testInfoCreateWithIntegerYear()
    {
        $info = Info::create(['year' => 2017]);
        $this->assertSame(2017, $info->year);
    }

    public function testInfoCreateWithStringYear()
    {
        $info = Info::create(['year' => "2017"]);
        $this->assertSame(2017, $info->year);
    }

    public function testInfoCreateWithFloatStringYear()
    {
        $info = Info::create(['year' => "2017.5"]);
        $this->assertSame(2017, $info->year);
    }

    public function testInfoCreateWithEmptyStringYear()
    {
        $info = Info::create(['year' => ""]);
        $this->assertNull($info->year);
    }

    public function testInfoCreateWithNonNumericStringYear()
    {
        $info = Info::create(['year' => "unknown"]);
        $this->assertNull($info->year);
    }

    public function testInfoCreateWithNullYear()
    {
        $info = Info::create(['year' => null]);
        $this->assertNull($info->year);
    }

    public function testInfoCreateWithoutYear()
    {
        $info = Info::create([]);
        $this->assertNull($info->year);
    }

    public function testInfoCreateWithOtherFields()
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

    public function testValidateBboxOnly()
    {
        // This should not throw an exception
        Annotation::validate([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => [10, 20, 30, 40],
        ]);
        $this->assertTrue(true); // If we get here, validation passed
    }

    public function testValidateSegmentationOnly()
    {
        // This should not throw an exception
        Annotation::validate([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'segmentation' => [1674.27, 528.23, 1853.22, 528.23, 1853.22, 799.04, 1674.27, 799.04]
        ]);
        $this->assertTrue(true); // If we get here, validation passed
    }

    public function testIsRectangleShapeBboxOnly()
    {
        $rectangleAnnotation = Annotation::create([
            'id' => 1,
            'image_id' => 1,
            'category_id' => 1,
            'bbox' => [1674.27, 528.23, 178.95, 270.81],
            'segmentation' => null
        ]);
        $this->assertTrue($rectangleAnnotation->isRectangleShape());
        $this->assertSame($rectangleAnnotation->getShape(), Shape::rectangle());

        // Test that getPoints() correctly converts bbox to points
        $expectedPoints = [
            1674.27, 528.23,           // top-left
            1853.22, 528.23,           // top-right (1674.27 + 178.95)
            1853.22, 799.04,           // bottom-right (528.23 + 270.81)
            1674.27, 799.04            // bottom-left
        ];
        $this->assertSame($expectedPoints, $rectangleAnnotation->getPoints());
    }
}
