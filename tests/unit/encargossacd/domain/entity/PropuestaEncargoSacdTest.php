<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\encargossacd\domain\value_objects\EncargoModoId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\encargossacd\PropuestaEncargoSacdFactory;
use Tests\myTest;

final class PropuestaEncargoSacdTest extends myTest
{
    public function test_es_un_encargo_sacd(): void
    {
        $this->assertInstanceOf(EncargoSacd::class, new PropuestaEncargoSacd());
    }

    public function test_id_nom_new_por_defecto_es_null(): void
    {
        $this->assertNull((new PropuestaEncargoSacd())->getId_nom_new());
    }

    public function test_set_and_get_id_nom_new(): void
    {
        $o = new PropuestaEncargoSacd();
        $o->setId_nom_new(10025);
        $this->assertSame(10025, $o->getId_nom_new());
        $o->setId_nom_new(null);
        $this->assertNull($o->getId_nom_new());
    }

    public function test_hereda_los_campos_de_encargo_sacd(): void
    {
        $o = (new PropuestaEncargoSacdFactory())->createSimple(9900001, 10099);
        $this->assertSame(9900001, $o->getId_item());
        $this->assertSame(3, $o->getId_enc());
        $this->assertSame(10011, $o->getId_nom());
        $this->assertSame(3, $o->getModoVo()->value());
        $this->assertSame(10099, $o->getId_nom_new());
        $this->assertNull($o->getF_fin());
    }

    public function test_from_array_hidrata_como_filas_de_bd(): void
    {
        $o = PropuestaEncargoSacd::fromArray([
            'id_item' => 5,
            'id_enc' => 7,
            'id_nom' => 10011,
            'modo' => 2,
            'f_ini' => new DateTimeLocal('2024-01-02'),
            'f_fin' => null,
            'id_nom_new' => 10012,
        ]);

        $this->assertSame(5, $o->getId_item());
        $this->assertSame(7, $o->getId_enc());
        $this->assertInstanceOf(EncargoModoId::class, $o->getModoVo());
        $this->assertSame(2, $o->getModoVo()->value());
        $this->assertSame('2024-01-02', $o->getF_ini()->format('Y-m-d'));
        $this->assertNull($o->getF_fin());
        $this->assertSame(10012, $o->getId_nom_new());
    }

    public function test_from_array_con_id_nom_new_null(): void
    {
        $o = PropuestaEncargoSacd::fromArray(['id_item' => 1, 'id_enc' => 1, 'id_nom' => 1, 'modo' => 1, 'f_ini' => new DateTimeLocal('2024-01-02'), 'id_nom_new' => null]);
        $this->assertNull($o->getId_nom_new());
    }
}
