<?php

declare(strict_types=1);

namespace Tests\integration\notas\application;

use src\notas\application\AsigFaltanPersonasSelectTablaData;
use src\notas\application\AsigFaltanSelectTablaData;
use Tests\myTest;

/**
 * Tablas «a quién le faltan N asignaturas» y «a quién le falta la asignatura X» sobre la BD real.
 * Se comprueba la validación de la entrada, el título según grupo y tramo y la forma de las filas.
 */
final class AsigFaltanTablasIntegracionTest extends myTest
{
    private AsigFaltanSelectTablaData $porNumero;
    private AsigFaltanPersonasSelectTablaData $porAsignatura;

    public function setUp(): void
    {
        parent::setUp();
        $this->porNumero = $GLOBALS['container']->get(AsigFaltanSelectTablaData::class);
        $this->porAsignatura = $GLOBALS['container']->get(AsigFaltanPersonasSelectTablaData::class);
    }

    private const CLAVES_FILA = ['id_nom', 'id_tabla', 'nom', 'nombre_ubi', 'stgr', 'telfs', 'mails'];

    // ---- por número de asignaturas ----------------------------------------------------------------------

    public function test_hay_que_marcar_un_grupo_de_personas(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->porNumero->execute(['numero' => '2', 'b_c' => 'b']);
    }

    /**
     * @dataProvider tramosProvider
     * @param array<string, string> $in
     */
    public function test_el_titulo_nombra_el_grupo_y_el_tramo(array $in, string $grupo, string $tramo): void
    {
        $r = $this->porNumero->execute($in + ['numero' => '1']);

        $this->assertStringContainsString($grupo, $r['titulo']);
        $this->assertStringContainsString($tramo, $r['titulo']);
    }

    /**
     * @return iterable<string, array{array<string, string>, string, string}>
     */
    public static function tramosProvider(): iterable
    {
        yield 'numerarios bienio' => [['personas_n' => '1', 'b_c' => 'b'], 'numerarios', 'bienio'];
        yield 'agregados cuadrienio por defecto' => [['personas_agd' => 'on'], 'agregados', 'cuadrienio'];
        yield 'ambos grupos, c1' => [['personas_n' => '1', 'personas_agd' => '1', 'c1' => '1', 'c2' => ''], 'numerarios y agregados', 'año I'];
        yield 'c2' => [['personas_n' => '1', 'b_c' => 'c', 'c1' => '', 'c2' => 'true'], 'numerarios', 'II-IV'];
    }

    public function test_la_forma_de_las_filas_depende_de_si_se_pide_la_lista(): void
    {
        $conteo = $this->porNumero->execute(['personas_n' => '1', 'b_c' => 'b', 'numero' => '3', 'lista' => '']);
        $lista = $this->porNumero->execute(['personas_n' => '1', 'b_c' => 'b', 'numero' => '3', 'lista' => '1']);

        $this->assertSame('PersonaN', $conteo['obj_pau']);
        $this->assertFalse($conteo['lista']);
        $this->assertTrue($lista['lista']);
        foreach ([$conteo, $lista] as $r) {
            foreach ($r['rows'] as $fila) {
                foreach (self::CLAVES_FILA as $clave) {
                    $this->assertArrayHasKey($clave, $fila);
                }
                $this->assertArrayHasKey('asig_txt', $fila);
                $this->assertIsInt($fila['id_nom']);
            }
        }
        $this->assertSame(count($conteo['rows']), count($lista['rows']));
    }

    // ---- por asignatura ----------------------------------------------------------------------

    /**
     * @param array<string, string> $extra
     * @return array<string, mixed>
     */
    private function entradaAsignatura(array $extra): array
    {
        return $extra + ['id_asignatura' => 1101, 'b_c' => 'b'];
    }

    public function test_por_asignatura_tambien_exige_un_grupo(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->porAsignatura->execute($this->entradaAsignatura([]));
    }

    public function test_por_asignatura_inexistente_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->porAsignatura->execute($this->entradaAsignatura(['personas_n' => '1', 'id_asignatura' => 99999]));
    }

    public function test_por_asignatura_devuelve_titulo_con_el_numero_de_filas_y_filas_completas(): void
    {
        $r = $this->porAsignatura->execute($this->entradaAsignatura(['personas_n' => '1']));

        $this->assertSame('PersonaN', $r['obj_pau']);
        $this->assertStringContainsString('(' . count($r['rows']) . ')', $r['titulo']);
        foreach ($r['rows'] as $fila) {
            $this->assertSame(self::CLAVES_FILA, array_keys($fila));
            $this->assertIsInt($fila['id_nom']);
        }
    }
}
