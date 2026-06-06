<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PeticionesGuardar;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;

final class PeticionesGuardarTest extends TestCase
{
    public function test_faltan_parametros(): void
    {
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->expects($this->never())->method('getPlazasPeticion');

        $msg = (new PeticionesGuardar($repo))->execute(['id_nom' => 1, 'sactividad' => '']);
        $this->assertNotSame('', $msg);
    }

    public function test_borra_anteriores_y_crea_nuevas(): void
    {
        $vieja = $this->createMock(PlazaPeticion::class);

        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([$vieja]);
        $repo->expects($this->once())->method('Eliminar')->with($vieja);

        $repo->method('findById')->willReturn(null);
        $repo->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $msg = (new PeticionesGuardar($repo))->execute([
            'id_nom' => 100,
            'sactividad' => 'ca',
            'actividades' => ['10', '0', '20'],
        ]);
        $this->assertSame('', $msg);
    }

    public function test_reutiliza_fila_existente_por_findById(): void
    {
        $existente = new PlazaPeticion();
        $existente->setId_nom(100);
        $existente->setId_activ(10);
        $existente->setOrden(9);
        $existente->setTipo('x');

        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([]);
        $repo->method('findById')->with(100, 10)->willReturn($existente);
        $repo->expects($this->once())->method('Guardar')->with($this->callback(function (PlazaPeticion $p) {
            return $p->getId_nom() === 100
                && $p->getId_activ() === 10
                && $p->getOrden() === 1
                && $p->getTipo() === 'ca';
        }))->willReturn(true);

        $this->assertSame('', (new PeticionesGuardar($repo))->execute([
            'id_nom' => 100,
            'sactividad' => 'ca',
            'actividades' => ['10'],
        ]));
    }

    public function test_error_si_guardar_falla(): void
    {
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([]);
        $repo->method('findById')->willReturn(null);
        $repo->method('Guardar')->willReturn(false);

        $msg = (new PeticionesGuardar($repo))->execute([
            'id_nom' => 1,
            'sactividad' => 'ca',
            'actividades' => ['5'],
        ]);
        $this->assertNotSame('', $msg);
    }
}
