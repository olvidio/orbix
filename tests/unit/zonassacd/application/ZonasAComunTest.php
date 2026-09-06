<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\zonassacd\application\ZonasAComun;

final class ZonasAComunTest extends TestCase
{
    public function test_esquema_comun_quita_el_sufijo_v(): void
    {
        $this->assertSame('H-dlb', ZonasAComun::esquemaComunDeSve('H-dlbv'));
        $this->assertSame('H-dlb', ZonasAComun::esquemaComunDeSve('H-dlb'));
    }
}
