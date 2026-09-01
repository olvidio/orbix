<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application\services;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\services\SincronizarCdCargosActiv;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivContexto;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivWriter;

final class SincronizarCdCargosActivTest extends TestCase
{
    private function contexto(): CdCargosActivContexto
    {
        return new CdCargosActivContexto($this->createMock(\PDO::class), '');
    }

    private function cargo(int $id_item, int $id_activ = 3001145, int $id_cargo = 3): ActividadCargo
    {
        $cargo = new ActividadCargo();
        $cargo->setId_item($id_item);
        $cargo->setId_activ($id_activ);
        $cargo->setId_cargo($id_cargo);
        $cargo->setPuede_agd(false);

        return $cargo;
    }

    public function test_cargo_con_id_item_hace_upsert_y_nunca_elimina(): void
    {
        $writer = $this->createMock(CdCargosActivWriter::class);
        $writer->expects($this->once())->method('upsert')->willReturn(true);
        $writer->expects($this->never())->method('eliminar');

        $servicio = new SincronizarCdCargosActiv($writer);

        $this->assertTrue($servicio->sincronizarCargo($this->cargo(12), $this->contexto()));
    }

    public function test_cargo_sin_id_item_elimina_y_nunca_hace_upsert(): void
    {
        $writer = $this->createMock(CdCargosActivWriter::class);
        $writer->expects($this->once())->method('eliminar')->with($this->anything(), 0)->willReturn(true);
        $writer->expects($this->never())->method('upsert');

        $servicio = new SincronizarCdCargosActiv($writer);
        $cargo = $this->cargo(12);
        $cargo->setId_item(0);

        $this->assertTrue($servicio->sincronizarCargo($cargo, $this->contexto()));
    }

    public function test_eliminar_cargo_llama_siempre_a_eliminar_con_el_id_item_correcto(): void
    {
        $writer = $this->createMock(CdCargosActivWriter::class);
        $writer->expects($this->once())->method('eliminar')->with($this->anything(), 12)->willReturn(true);
        $writer->expects($this->never())->method('upsert');

        $servicio = new SincronizarCdCargosActiv($writer);

        $this->assertTrue($servicio->eliminarCargo($this->cargo(12), $this->contexto()));
    }
}
