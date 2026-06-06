<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\RelacionTarifaUpdate;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;

final class RelacionTarifaUpdateTest extends TestCase
{
    public function test_falta_tarifa(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $this->assertNotSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_tarifa' => 0,
            'id_tipo_activ' => 123456,
        ]));
    }

    public function test_falta_tipo_actividad(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $this->assertNotSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_tarifa' => 1,
            'id_tipo_activ' => 0,
        ]));
    }

    public function test_nuevo(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(99);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (RelacionTarifaTipoActividad $r) {
            return $r->getId_item() === 99
                && $r->getId_tarifa() === 5
                && $r->getId_tipo_activ() === 123456;
        });

        $this->assertSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_item' => 'nuevo',
            'id_tarifa' => 5,
            'id_tipo_activ' => 123456,
        ]));
    }

    public function test_actualizar_no_encontrada(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn(null);

        $this->assertNotSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_item' => '7',
            'id_tarifa' => 5,
            'id_tipo_activ' => 123456,
        ]));
    }
}
