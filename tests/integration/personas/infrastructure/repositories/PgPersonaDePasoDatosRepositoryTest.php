<?php

declare(strict_types=1);

namespace Tests\integration\personas\infrastructure\repositories;

use PHPUnit\Framework\Attributes\DataProvider;
use src\personas\domain\contracts\PersonaDePasoDatosRepositoryInterface as Datos;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Solo lee. Usa personas de paso (id negativo) que ya tienen datos en las BD de pruebas.
 */
final class PgPersonaDePasoDatosRepositoryTest extends myTest
{
    private Datos $repo;

    public function setUp(): void
    {
        parent::setUp();
        $repo = $GLOBALS['container']->get(Datos::class);
        $this->assertInstanceOf(Datos::class, $repo);
        $this->repo = $repo;
    }

    public function test_una_persona_sin_datos_no_tiene_ninguno(): void
    {
        $this->assertSame([], $this->repo->datosDe(-999999001));
    }

    /** @return iterable<string, array{0: string, 1: string, 2: string, 3?: string}> */
    public static function origenes(): iterable
    {
        // Las asistencias se buscan por la madre común: en producción `global.d_asistentes_dl` no es legible.
        yield 'asistencia en una dl' => [Datos::ASISTENCIAS, 'oDBE_Select', 'publicv.d_asistentes_all', "tableoid::regclass::text LIKE '%d_asistentes_dl'"];
        yield 'asistencia out o de paso' => [Datos::ASISTENCIAS, 'oDBE_Select', 'publicv.d_asistentes_all', "tableoid::regclass::text NOT LIKE '%d_asistentes_dl'"];
        yield 'cargo' => [Datos::CARGOS, 'oDBE_Select', 'global.d_cargos_activ'];
        yield 'encargo' => [Datos::ENCARGOS, 'oDBE_Select', 'global.encargos_sacd'];
        yield 'nota' => [Datos::NOTAS, 'oDB', 'publicv.e_notas'];
        yield 'matrícula' => [Datos::MATRICULAS, 'oDB', 'publicv.d_matriculas_activ'];
        yield 'docencia' => [Datos::DOCENCIA, 'oDB', 'global.d_docencia_stgr'];
        yield 'zona' => [Datos::ZONAS, 'oDBC_Select', 'global.zonas_sacd'];
    }

    #[DataProvider('origenes')]
    public function test_detecta_cada_tipo_de_dato(string $tipo, string $conexion, string $tabla, string $filtro = 'true'): void
    {
        $st = GlobalPdo::get($conexion)->query("SELECT id_nom FROM $tabla WHERE id_nom < 0 AND $filtro LIMIT 1");
        $idNom = $st === false ? false : $st->fetchColumn();
        if ($idNom === false) {
            $this->markTestIncomplete("En las BD de pruebas no hay ninguna persona de paso en $tabla");
        }

        $this->assertContains($tipo, $this->repo->datosDe((int) $idNom));
    }
}
