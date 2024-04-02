<?php

namespace Biigle\Tests\Modules\Module;

use Biigle\Modules\Module\ModuleServiceProvider;
use TestCase;

class ModuleServiceProviderTest extends TestCase
{
    public function testServiceProvider()
    {
        $this->assertTrue(class_exists(ModuleServiceProvider::class));
    }
}
