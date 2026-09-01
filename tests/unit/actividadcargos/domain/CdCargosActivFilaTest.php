<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\domain;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\CdCargosActivFila;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\value_objects\ObservacionesCargo;

final class CdCargosActivFilaTest extends TestCase
{
    public function test_desde_registro_rellena_a_null_las_columnas_ausentes(): void
    {
        $registro = [
            'id_item' => 12,
            'id_activ' => 3001145,
            'id_cargo' => 3,
        ];

        $fila = CdCargosActivFila::desdeRegistro($registro);

        $this->assertNull($fila['id_nom']);
        $this->assertNull($fila['observ']);
        $this->assertSame(12, $fila['id_item']);
        $this->assertSame(3001145, $fila['id_activ']);
    }

    public function test_desde_registro_descarta_columnas_que_no_estan_en_columnas(): void
    {
        $registro = [
            'id_item' => 12,
            'id_schema' => 1001,
            'domainEvents' => [],
        ];

        $fila = CdCargosActivFila::desdeRegistro($registro);

        $this->assertSame(CdCargosActivFila::COLUMNAS, array_keys($fila));
        $this->assertArrayNotHasKey('id_schema', $fila);
        $this->assertArrayNotHasKey('domainEvents', $fila);
    }

    public function test_desde_cargo_toma_los_campos_de_la_entidad(): void
    {
        $cargo = new ActividadCargo();
        $cargo->setId_item(12);
        $cargo->setId_activ(3001145);
        $cargo->setId_cargo(3);
        $cargo->setId_nom(2001);
        $cargo->setPuede_agd(true);
        $cargo->setObservVo(new ObservacionesCargo('nota'));

        $fila = CdCargosActivFila::desdeCargo($cargo);

        $this->assertSame(12, $fila['id_item']);
        $this->assertSame(3001145, $fila['id_activ']);
        $this->assertSame(3, $fila['id_cargo']);
        $this->assertSame(2001, $fila['id_nom']);
        $this->assertSame('nota', $fila['observ']);
        $this->assertTrue(CdCargosActivFila::esVerdadero($fila['puede_agd']));
    }

    public function test_id_item_con_valor_valido(): void
    {
        $this->assertSame(12, CdCargosActivFila::idItem(['id_item' => 12]));
        $this->assertSame(12, CdCargosActivFila::idItem(['id_item' => '12']));
    }

    public function test_id_item_cero_o_nulo_da_cero(): void
    {
        $this->assertSame(0, CdCargosActivFila::idItem(['id_item' => 0]));
        $this->assertSame(0, CdCargosActivFila::idItem(['id_item' => null]));
        $this->assertSame(0, CdCargosActivFila::idItem([]));
        $this->assertSame(0, CdCargosActivFila::idItem(['id_item' => 'abc']));
    }

    public function test_debe_copiarse_con_id_item_valido(): void
    {
        $this->assertTrue(CdCargosActivFila::debeCopiarse(['id_item' => 12]));
        $this->assertFalse(CdCargosActivFila::debeCopiarse(['id_item' => 0]));
        $this->assertFalse(CdCargosActivFila::debeCopiarse([]));
    }

    public function test_es_verdadero_con_valores_verdaderos(): void
    {
        $this->assertTrue(CdCargosActivFila::esVerdadero(true));
        $this->assertTrue(CdCargosActivFila::esVerdadero('t'));
        $this->assertTrue(CdCargosActivFila::esVerdadero('true'));
        $this->assertTrue(CdCargosActivFila::esVerdadero('1'));
        $this->assertTrue(CdCargosActivFila::esVerdadero(1));
    }

    public function test_es_verdadero_con_valores_falsos(): void
    {
        $this->assertFalse(CdCargosActivFila::esVerdadero(false));
        $this->assertFalse(CdCargosActivFila::esVerdadero('f'));
        $this->assertFalse(CdCargosActivFila::esVerdadero(''));
        $this->assertFalse(CdCargosActivFila::esVerdadero(null));
    }

    private function filaBase(): array
    {
        $fila = [];
        foreach (CdCargosActivFila::COLUMNAS as $columna) {
            $fila[$columna] = null;
        }
        $fila['id_item'] = 12;
        $fila['id_activ'] = 3001145;
        $fila['id_cargo'] = 3;
        $fila['id_nom'] = 2001;
        $fila['puede_agd'] = false;

        return $fila;
    }

    public function test_dos_filas_iguales_no_dan_diferencias(): void
    {
        $fila = $this->filaBase();

        $this->assertSame([], CdCargosActivFila::diferencias($fila, $fila));
    }

    public function test_null_y_cadena_vacia_son_equivalentes(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $origen['observ'] = null;
        $destino['observ'] = '';

        $this->assertSame([], CdCargosActivFila::diferencias($origen, $destino));
    }

    public function test_true_y_t_son_equivalentes_en_puede_agd(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $origen['puede_agd'] = true;
        $destino['puede_agd'] = 't';

        $this->assertSame([], CdCargosActivFila::diferencias($origen, $destino));
    }

    public function test_entero_y_su_representacion_como_cadena_son_equivalentes(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $origen['id_nom'] = 2001;
        $destino['id_nom'] = '2001';

        $this->assertSame([], CdCargosActivFila::diferencias($origen, $destino));
    }

    public function test_reporta_una_diferencia_real(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $destino['id_nom'] = 2002;

        $this->assertSame(['id_nom'], CdCargosActivFila::diferencias($origen, $destino));
    }
}
