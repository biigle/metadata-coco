<?php

namespace Biigle\Tests\Modules\Module\Http\Controllers;

use Biigle\Tests\UserTest;
use TestCase;

class QuotesControllerTest extends TestCase
{
    public function testRoute()
    {
        $user = UserTest::create();

        // Redirect to login page.
        $this->get('quotes')->assertStatus(302);

        $this->be($user);
        $this->get('quotes')->assertStatus(200);
    }

    public function testQuoteProvider()
    {
        $user = UserTest::create();

        // Redirect to login page.
        $this->get('quotes/new')->assertStatus(302);

        $this->be($user);
        $this->get('quotes/new')->assertStatus(200);
    }
}
