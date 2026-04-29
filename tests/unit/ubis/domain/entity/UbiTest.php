<?php

declare(strict_types=1);

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\Ubi;
use Tests\myTest;

final class UbiTest extends myTest
{
    public function test_constructor_and_instance(): void
    {
        $ubi = new Ubi();
        $this->assertInstanceOf(Ubi::class, $ubi);
    }
}
