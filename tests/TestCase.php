<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** The bundle is the browser suite's business; this one must run without a node build. */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
