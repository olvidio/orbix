<?php

declare(strict_types=1);

namespace Tests\unit\ubis\domain;

use PHPUnit\Framework\TestCase;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\CuCentrosFila;

final class CuCentrosFilaTest extends TestCase
{
    // --- Reparto entre las dos copias -------------------------------------

    public function test_los_centros_que_empiezan_por_2_son_de_sf(): void
    {
        $this->assertTrue(CuCentrosFila::esDeSf(2001234));
        $this->assertTrue(CuCentrosFila::esDeSf('2001234'));
        $this->assertFalse(CuCentrosFila::esDeSf(1001234));
        $this->assertFalse(CuCentrosFila::esDeSf(''));
    }

    public function test_cada_copia_se_queda_solo_con_los_centros_de_su_lado(): void
    {
        $centroSv = ['id_ubi' => 1001234];
        $centroSf = ['id_ubi' => 2001234];

        $this->assertTrue(CuCentrosFila::debeCopiarse($centroSv, false));
        $this->assertFalse(CuCentrosFila::debeCopiarse($centroSv, true));
        $this->assertTrue(CuCentrosFila::debeCopiarse($centroSf, true));
        $this->assertFalse(CuCentrosFila::debeCopiarse($centroSf, false));
    }

    public function test_un_centro_sin_id_ubi_utilizable_no_entra_en_ninguna_copia(): void
    {
        $this->assertFalse(CuCentrosFila::debeCopiarse([], false));
        $this->assertFalse(CuCentrosFila::debeCopiarse([], true));
        $this->assertFalse(CuCentrosFila::debeCopiarse(['id_ubi' => 0], false));
        $this->assertFalse(CuCentrosFila::debeCopiarse(['id_ubi' => 'abc'], true));
    }

    public function test_cada_definicion_apunta_a_su_tabla(): void
    {
        $this->assertSame(CuCentrosFila::TABLA_SV, CuCentrosFila::definicionSv()->tabla);
        $this->assertSame(CuCentrosFila::TABLA_SF, CuCentrosFila::definicionSf()->tabla);
        $this->assertSame(CuCentrosFila::TABLA_SF, CuCentrosFila::definicionDe(2001234)->tabla);
        $this->assertSame(CuCentrosFila::TABLA_SV, CuCentrosFila::definicionDe(1001234)->tabla);
    }

    public function test_ninguna_copia_tiene_columnas_del_destino(): void
    {
        foreach ([CuCentrosFila::definicionSv(), CuCentrosFila::definicionSf()] as $definicion) {
            $this->assertSame([], $definicion->columnasDelDestino);
        }
    }

    public function test_una_fila_del_origen_no_arrastra_columnas_ajenas(): void
    {
        $fila = CuCentrosFila::desdeRegistro(['id_ubi' => 1001234, 'id_zona' => 7]);

        $this->assertArrayNotHasKey('id_zona', $fila);
        $this->assertSame(CuCentrosFila::COLUMNAS, array_keys($fila));
    }

    // --- Proyección --------------------------------------------------------

    public function test_desde_registro_descarta_las_columnas_del_interior(): void
    {
        $registro = [
            'id_ubi' => 1001234,
            'nombre_ubi' => 'Centro A',
            'observ' => 'sólo interesa dentro',
            'num_pi' => 3,
            'plazas' => 20,
        ];

        $fila = CuCentrosFila::desdeRegistro($registro);

        $this->assertSame(1001234, $fila['id_ubi']);
        $this->assertSame('Centro A', $fila['nombre_ubi']);
        $this->assertArrayNotHasKey('observ', $fila);
        $this->assertArrayNotHasKey('num_pi', $fila);
        $this->assertArrayNotHasKey('plazas', $fila);
    }

    public function test_desde_centro_proyecta_la_entidad_y_convierte_f_active(): void
    {
        // La entidad real (`CentroDl`) pide repositorios al contenedor en el
        // constructor; aquí basta con algo que exponga `toArrayForDatabase()`
        // como ella, con `f_active` en el tipo del dominio.
        $centro = $this->centroFalso([
            'id_ubi' => 1001234,
            'nombre_ubi' => 'Centro A',
            'dl' => 'dlb',
            'active' => true,
            'f_active' => new DateTimeLocal('2026-01-15'),
            'sv' => true,
            'cdc' => false,
            'id_zona' => 7,
            'observ' => 'sólo interesa dentro',
        ]);

        $fila = CuCentrosFila::desdeCentro($centro);

        $this->assertSame(1001234, $fila['id_ubi']);
        $this->assertSame('Centro A', $fila['nombre_ubi']);
        $this->assertSame('dlb', $fila['dl']);
        $this->assertTrue(CuCentrosFila::esVerdadero($fila['active']));
        $this->assertTrue(CuCentrosFila::esVerdadero($fila['sv']));
        $this->assertFalse(CuCentrosFila::esVerdadero($fila['cdc']));
        // El mismo converter que usa el repositorio de origen para esa columna.
        $this->assertSame('2026-01-15', $fila['f_active']);
        $this->assertArrayNotHasKey('id_zona', $fila);
        $this->assertArrayNotHasKey('observ', $fila);
    }

    public function test_desde_centro_rechaza_algo_que_no_sea_una_entidad(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CuCentrosFila::desdeCentro(new \stdClass());
    }

    public function test_los_booleanos_que_admiten_nulo_se_escriben_como_nulo(): void
    {
        $fila = CuCentrosFila::definicionSv()->paraEscribir(
            CuCentrosFila::desdeRegistro(['id_ubi' => 1001234, 'active' => false]),
        );

        $this->assertSame('f', $fila['active']);
        $this->assertNull($fila['sv']);
        $this->assertNull($fila['sf']);
        $this->assertNull($fila['cdc']);
    }

    /**
     * Doble de entidad de centro: aplica los converters igual que `Hydratable`.
     *
     * @param array<string, mixed> $datos
     */
    private function centroFalso(array $datos): object
    {
        return new class ($datos) {
            /** @param array<string, mixed> $datos */
            public function __construct(private readonly array $datos)
            {
            }

            /**
             * @param array<string, callable> $converters
             * @return array<string, mixed>
             */
            public function toArrayForDatabase(array $converters = []): array
            {
                $aDatos = $this->datos;
                foreach ($converters as $columna => $converter) {
                    if (array_key_exists($columna, $aDatos)) {
                        $aDatos[$columna] = $converter($aDatos[$columna]);
                    }
                }

                return $aDatos;
            }
        };
    }

    // --- Comparación -------------------------------------------------------

    /** @return array<string, mixed> */
    private function filaBase(): array
    {
        $fila = [];
        foreach (CuCentrosFila::COLUMNAS as $columna) {
            $fila[$columna] = null;
        }
        $fila['id_ubi'] = 1001234;
        $fila['tipo_ubi'] = 'ctrdl';
        $fila['nombre_ubi'] = 'Centro A';
        $fila['dl'] = 'dlb';
        $fila['active'] = true;
        $fila['f_active'] = '2026-01-15';

        return $fila;
    }

    public function test_dos_filas_iguales_no_dan_diferencias(): void
    {
        $this->assertSame([], CuCentrosFila::diferencias($this->filaBase(), $this->filaBase()));
    }

    public function test_los_valores_equivalentes_de_postgres_no_dan_diferencias(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $destino['id_ubi'] = '1001234';
        $destino['active'] = 't';
        $destino['tipo_labor'] = '';

        $this->assertSame([], CuCentrosFila::diferencias($origen, $destino));
    }

    public function test_reporta_las_columnas_que_difieren_de_verdad(): void
    {
        $origen = $this->filaBase();
        $destino = $this->filaBase();
        $destino['nombre_ubi'] = 'Centro B';
        $destino['active'] = false;

        $this->assertSame(['nombre_ubi', 'active'], CuCentrosFila::diferencias($origen, $destino));
    }
}
