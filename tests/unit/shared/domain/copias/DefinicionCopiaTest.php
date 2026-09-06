<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\copias;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\shared\domain\copias\DefinicionCopia;

final class DefinicionCopiaTest extends TestCase
{
    private function definicion(bool $claveAdmiteNegativos = false): DefinicionCopia
    {
        return new DefinicionCopia(
            tabla: 'cp_prueba',
            clave: 'id_nom',
            columnas: ['id_nom', 'nombre', 'activo', 'observ'],
            columnasBooleanas: ['activo'],
            claveAdmiteNegativos: $claveAdmiteNegativos,
        );
    }

    // --- Construcción ---------------------------------------------------

    public function test_la_clave_debe_estar_entre_las_columnas_copiadas(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DefinicionCopia('cp_prueba', 'id_ausente', ['id_nom', 'nombre']);
    }

    public function test_las_columnas_booleanas_deben_estar_entre_las_copiadas(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DefinicionCopia('cp_prueba', 'id_nom', ['id_nom'], ['no_existe']);
    }

    public function test_tabla_y_clave_no_pueden_ir_vacias(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DefinicionCopia('', 'id_nom', ['id_nom']);
    }

    public function test_una_columna_no_puede_ser_a_la_vez_del_destino_y_copiada(): void
    {
        // Sería contradictorio: la reconciliación la sobrescribiría con el origen.
        $this->expectException(InvalidArgumentException::class);

        new DefinicionCopia(
            tabla: 'cu_prueba',
            clave: 'id_ubi',
            columnas: ['id_ubi', 'id_zona'],
            columnasDelDestino: ['id_zona'],
        );
    }

    // --- Columnas del destino --------------------------------------------

    public function test_las_columnas_del_destino_no_se_escriben_ni_se_comparan(): void
    {
        $definicion = new DefinicionCopia(
            tabla: 'cu_prueba',
            clave: 'id_ubi',
            columnas: ['id_ubi', 'nombre_ubi'],
            columnasDelDestino: ['id_zona'],
        );

        $fila = $definicion->paraEscribir(['id_ubi' => 1, 'nombre_ubi' => 'A', 'id_zona' => 9]);

        $this->assertArrayNotHasKey('id_zona', $fila);
        $this->assertNotContains('id_zona', $definicion->columnasActualizables());
        $this->assertSame(
            [],
            $definicion->diferencias(
                ['id_ubi' => 1, 'nombre_ubi' => 'A', 'id_zona' => null],
                ['id_ubi' => 1, 'nombre_ubi' => 'A', 'id_zona' => 9],
            ),
        );
    }

    // --- desdeRegistro() -------------------------------------------------

    public function test_desde_registro_rellena_a_null_las_columnas_ausentes(): void
    {
        $fila = $this->definicion()->desdeRegistro(['id_nom' => 7, 'nombre' => 'Ana']);

        $this->assertSame(['id_nom', 'nombre', 'activo', 'observ'], array_keys($fila));
        $this->assertNull($fila['activo']);
        $this->assertNull($fila['observ']);
    }

    public function test_desde_registro_descarta_columnas_no_copiadas(): void
    {
        $fila = $this->definicion()->desdeRegistro(['id_nom' => 7, 'id_auto' => 99, 'edad' => 42]);

        $this->assertArrayNotHasKey('id_auto', $fila);
        $this->assertArrayNotHasKey('edad', $fila);
    }

    // --- valorClave() ----------------------------------------------------

    public function test_valor_clave_acepta_entero_y_cadena_numerica(): void
    {
        $this->assertSame(7, $this->definicion()->valorClave(['id_nom' => 7]));
        $this->assertSame(7, $this->definicion()->valorClave(['id_nom' => '7']));
    }

    public function test_valor_clave_da_cero_si_falta_o_no_es_numerica(): void
    {
        $this->assertSame(0, $this->definicion()->valorClave([]));
        $this->assertSame(0, $this->definicion()->valorClave(['id_nom' => null]));
        $this->assertSame(0, $this->definicion()->valorClave(['id_nom' => 'abc']));
    }

    public function test_por_defecto_una_clave_negativa_no_es_valida(): void
    {
        $this->assertSame(0, $this->definicion()->valorClave(['id_nom' => -5]));
    }

    public function test_con_claveAdmiteNegativos_la_clave_negativa_es_valida(): void
    {
        // Es el caso de las personas de paso, cuyo id_nom es negativo.
        $this->assertSame(-5, $this->definicion(true)->valorClave(['id_nom' => -5]));
        $this->assertSame(0, $this->definicion(true)->valorClave(['id_nom' => 0]));
    }

    // --- paraEscribir() --------------------------------------------------

    public function test_para_escribir_convierte_los_booleanos_a_t_o_f(): void
    {
        $definicion = $this->definicion();

        // PDO bindea el bool falso como '', que Postgres rechaza como boolean.
        $this->assertSame('t', $definicion->paraEscribir(['id_nom' => 1, 'activo' => true])['activo']);
        $this->assertSame('f', $definicion->paraEscribir(['id_nom' => 1, 'activo' => false])['activo']);
    }

    public function test_para_escribir_conserva_el_nulo_de_una_columna_booleana(): void
    {
        // Hay booleanos que admiten nulo (`sv`, `sf`, `cdc` en centros): pasarlos
        // a 'f' cambiaría el dato que tiene el origen.
        $this->assertNull($this->definicion()->paraEscribir(['id_nom' => 1])['activo']);
        $this->assertNull($this->definicion()->paraEscribir(['id_nom' => 1, 'activo' => null])['activo']);
    }

    public function test_para_escribir_no_toca_las_columnas_no_booleanas(): void
    {
        $fila = $this->definicion()->paraEscribir(['id_nom' => 1, 'nombre' => 'Ana', 'observ' => null]);

        $this->assertSame('Ana', $fila['nombre']);
        $this->assertNull($fila['observ']);
    }

    // --- diferencias() ---------------------------------------------------

    private function filaBase(): array
    {
        return ['id_nom' => 7, 'nombre' => 'Ana', 'activo' => true, 'observ' => null];
    }

    public function test_dos_filas_iguales_no_dan_diferencias(): void
    {
        $this->assertSame([], $this->definicion()->diferencias($this->filaBase(), $this->filaBase()));
    }

    public function test_null_y_cadena_vacia_son_equivalentes(): void
    {
        $destino = ['id_nom' => 7, 'nombre' => 'Ana', 'activo' => true, 'observ' => ''];

        $this->assertSame([], $this->definicion()->diferencias($this->filaBase(), $destino));
    }

    public function test_true_y_t_son_equivalentes_en_una_columna_booleana(): void
    {
        $destino = ['id_nom' => 7, 'nombre' => 'Ana', 'activo' => 't', 'observ' => null];

        $this->assertSame([], $this->definicion()->diferencias($this->filaBase(), $destino));
    }

    public function test_entero_y_su_representacion_como_cadena_son_equivalentes(): void
    {
        $origen = ['id_nom' => 7, 'nombre' => 'Ana', 'activo' => true, 'observ' => 3];
        $destino = ['id_nom' => '7', 'nombre' => 'Ana', 'activo' => true, 'observ' => '3'];

        $this->assertSame([], $this->definicion()->diferencias($origen, $destino));
    }

    public function test_reporta_solo_las_columnas_que_difieren_de_verdad(): void
    {
        $destino = ['id_nom' => 7, 'nombre' => 'Berta', 'activo' => false, 'observ' => null];

        $this->assertSame(['nombre', 'activo'], $this->definicion()->diferencias($this->filaBase(), $destino));
    }

    // --- columnasActualizables() -----------------------------------------

    public function test_las_columnas_actualizables_excluyen_la_clave(): void
    {
        // La clave va en el WHERE del UPDATE, no en el SET.
        $this->assertSame(['nombre', 'activo', 'observ'], $this->definicion()->columnasActualizables());
    }
}
