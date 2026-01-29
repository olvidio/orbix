<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\UbiDireccion;
use Tests\myTest;

class UbiDireccionTest extends myTest
{
    private UbiDireccion $UbiDireccion;

    public function setUp(): void
    {
        parent::setUp();

        $a = [
            'id_ubi' => 1,
            'id_direccion' => 1,
            'propietario' => true,
            'principal' => true,
        ];

        $this->UbiDireccion = new UbiDireccion($a);
    }

    public function test_set_and_get_id_ubi()
    {
        $this->UbiDireccion->setIdUbi(1);
        $this->assertEquals(1, $this->UbiDireccion->getIdUbi());
    }

    public function test_set_and_get_id_direccion()
    {
        $this->UbiDireccion->setIdDireccion(1);
        $this->assertEquals(1, $this->UbiDireccion->getIdDireccion());
    }

    public function test_set_and_get_propietario()
    {
        $this->UbiDireccion->setPropietario(true);
        $this->assertTrue($this->UbiDireccion->isPropietario());
    }

    public function test_set_and_get_principal()
    {
        $this->UbiDireccion->setPrincipal(true);
        $this->assertTrue($this->UbiDireccion->isPrincipal());
    }

    public function test_constructor_with_array()
    {
        $ubiDireccion = new UbiDireccion([
            'id_ubi' => 1,
            'id_direccion' => 1,
            'propietario' => true,
            'principal' => true,
        ]);

        $this->assertEquals(1, $ubiDireccion->getIdUbi());
        $this->assertEquals(1, $ubiDireccion->getIdDireccion());
        $this->assertTrue($ubiDireccion->isPropietario());
        $this->assertTrue($ubiDireccion->isPrincipal());
    }

    public function test_constructor_with_partial_array()
    {
        $ubiDireccion = new UbiDireccion([
            'id_ubi' => 1,
        ]);

        $this->assertEquals(1, $ubiDireccion->getIdUbi());
        $this->assertNull($ubiDireccion->getIdDireccion());
        $this->assertNull($ubiDireccion->isPropietario());
        $this->assertNull($ubiDireccion->isPrincipal());
    }
}
