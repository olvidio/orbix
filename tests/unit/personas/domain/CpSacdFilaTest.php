<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain;

use PHPUnit\Framework\TestCase;
use src\personas\domain\CpSacdFila;

final class CpSacdFilaTest extends TestCase
{
    // --- desdeRegistro() ---------------------------------------------------

    public function test_desde_registro_rellena_a_null_las_columnas_ausentes(): void
    {
        // Un registro de p_de_paso_ex no tiene id_ctr.
        $registro = [
            'id_nom' => 10021,
            'id_tabla' => 'pn',
            'dl' => 'dlb',
            'sacd' => true,
            'apellido1' => 'Perez',
        ];

        $fila = CpSacdFila::desdeRegistro($registro);

        $this->assertNull($fila['id_ctr']);
        $this->assertSame(10021, $fila['id_nom']);
        $this->assertSame('Perez', $fila['apellido1']);
    }

    public function test_desde_registro_descarta_columnas_que_no_estan_en_columnas(): void
    {
        $registro = [
            'id_nom' => 10021,
            'id_tabla' => 'pn',
            'edad' => 42,
            'profesor_stgr' => true,
            'id_auto' => 999,
        ];

        $fila = CpSacdFila::desdeRegistro($registro);

        $this->assertSame(CpSacdFila::COLUMNAS, array_keys($fila));
        $this->assertArrayNotHasKey('edad', $fila);
        $this->assertArrayNotHasKey('profesor_stgr', $fila);
        $this->assertArrayNotHasKey('id_auto', $fila);
    }

    // --- idNom() -------------------------------------------------------------

    public function test_id_nom_con_valor_valido(): void
    {
        $this->assertSame(2001, CpSacdFila::idNom(['id_nom' => 2001]));
    }

    public function test_id_nom_con_string_numerico(): void
    {
        $this->assertSame(2001, CpSacdFila::idNom(['id_nom' => '2001']));
    }

    public function test_id_nom_negativo_es_valido_en_de_paso(): void
    {
        $this->assertSame(-100161351, CpSacdFila::idNom(['id_nom' => -100161351]));
        $this->assertSame(-100161351, CpSacdFila::idNom(['id_nom' => '-100161351']));
    }

    public function test_id_nom_con_null_da_cero(): void
    {
        $this->assertSame(0, CpSacdFila::idNom(['id_nom' => null]));
        $this->assertSame(0, CpSacdFila::idNom([]));
    }

    public function test_id_nom_con_basura_no_numerica_da_cero(): void
    {
        $this->assertSame(0, CpSacdFila::idNom(['id_nom' => 'abc']));
    }

    // --- esVerdadero() ---------------------------------------------------

    public function test_es_verdadero_con_valores_verdaderos(): void
    {
        $this->assertTrue(CpSacdFila::esVerdadero(true));
        $this->assertTrue(CpSacdFila::esVerdadero('t'));
        $this->assertTrue(CpSacdFila::esVerdadero('true'));
        $this->assertTrue(CpSacdFila::esVerdadero('1'));
        $this->assertTrue(CpSacdFila::esVerdadero(1));
    }

    public function test_es_verdadero_con_valores_falsos(): void
    {
        $this->assertFalse(CpSacdFila::esVerdadero(false));
        $this->assertFalse(CpSacdFila::esVerdadero('f'));
        $this->assertFalse(CpSacdFila::esVerdadero(''));
        $this->assertFalse(CpSacdFila::esVerdadero(null));
    }

    // --- debeCopiarse() ---------------------------------------------------

    public function test_numerario_con_sacd_verdadero_entra(): void
    {
        $fila = ['id_tabla' => 'n', 'sacd' => true, 'dl' => 'dlb'];

        $this->assertTrue(CpSacdFila::debeCopiarse($fila, 'dlb'));
    }

    public function test_numerario_con_sacd_falso_no_entra(): void
    {
        $fila = ['id_tabla' => 'n', 'sacd' => false, 'dl' => 'dlb'];

        $this->assertFalse(CpSacdFila::debeCopiarse($fila, 'dlb'));
    }

    public function test_id_tabla_no_contemplado_no_entra(): void
    {
        $filaS = ['id_tabla' => 's', 'sacd' => true, 'dl' => 'dlb'];
        $filaNax = ['id_tabla' => 'nax', 'sacd' => true, 'dl' => 'dlb'];

        $this->assertFalse(CpSacdFila::debeCopiarse($filaS, 'dlb'));
        $this->assertFalse(CpSacdFila::debeCopiarse($filaNax, 'dlb'));
    }

    public function test_de_paso_entra_solo_si_la_dl_coincide_con_la_propia(): void
    {
        $filaPn = ['id_tabla' => 'pn', 'sacd' => true, 'dl' => 'dlb'];
        $filaPa = ['id_tabla' => 'pa', 'sacd' => true, 'dl' => 'dlb'];

        $this->assertTrue(CpSacdFila::debeCopiarse($filaPn, 'dlb'));
        $this->assertTrue(CpSacdFila::debeCopiarse($filaPa, 'dlb'));
    }

    public function test_de_paso_de_otra_dl_no_entra(): void
    {
        $fila = ['id_tabla' => 'pn', 'sacd' => true, 'dl' => 'dlc'];

        $this->assertFalse(CpSacdFila::debeCopiarse($fila, 'dlb'));
    }

    public function test_de_paso_no_entra_si_la_dl_propia_esta_vacia(): void
    {
        $fila = ['id_tabla' => 'pn', 'sacd' => true, 'dl' => ''];

        $this->assertFalse(CpSacdFila::debeCopiarse($fila, ''));
    }

    // --- diferencias() ---------------------------------------------------

    private function filaBase(): array
    {
        $fila = [];
        foreach (CpSacdFila::COLUMNAS as $columna) {
            $fila[$columna] = null;
        }
        $fila['id_nom'] = 2001;
        $fila['apellido1'] = 'Perez';
        $fila['sacd'] = true;
        $fila['id_ctr'] = 3;

        return $fila;
    }

    public function test_dos_filas_iguales_no_dan_diferencias(): void
    {
        $fila = $this->filaBase();

        $this->assertSame([], CpSacdFila::diferencias($fila, $fila));
    }

    public function test_null_y_cadena_vacia_son_equivalentes(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $origen['observ'] = null;
        $destino['observ'] = '';

        $this->assertSame([], CpSacdFila::diferencias($origen, $destino));
    }

    public function test_true_y_t_son_equivalentes_en_sacd(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $origen['sacd'] = true;
        $destino['sacd'] = 't';

        $this->assertSame([], CpSacdFila::diferencias($origen, $destino));
    }

    public function test_entero_y_su_representacion_como_cadena_son_equivalentes(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $origen['id_ctr'] = 3;
        $destino['id_ctr'] = '3';

        $this->assertSame([], CpSacdFila::diferencias($origen, $destino));
    }

    public function test_reporta_una_diferencia_real(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $destino['apellido1'] = 'Gomez';

        $this->assertSame(['apellido1'], CpSacdFila::diferencias($origen, $destino));
    }
}
