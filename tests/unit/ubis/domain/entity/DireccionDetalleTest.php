<?php

declare(strict_types=1);

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\DireccionDetalle;
use Tests\myTest;

final class DireccionDetalleTest extends myTest
{
    public function test_default_constructor_leaves_null_flags(): void
    {
        $d = new DireccionDetalle();
        $this->assertNull($d->getDireccionVo());
        $this->assertNull($d->isPrincipal());
        $this->assertNull($d->isPropietario());
    }

    public function test_constructor_from_array(): void
    {
        $d = new DireccionDetalle([
            'principal' => true,
            'propietario' => false,
        ]);
        $this->assertTrue($d->isPrincipal());
        $this->assertFalse($d->isPropietario());
    }

    public function test_set_and_get_principal_propietario(): void
    {
        $d = new DireccionDetalle();
        $d->setPrincipal(true);
        $d->setPropietario(false);
        $this->assertTrue($d->isPrincipal());
        $this->assertFalse($d->isPropietario());
    }
}
