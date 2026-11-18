<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\ui\http;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\ui\http\AccesoRolPau;

/**
 * Un usuario de una casa (pau `cdc`) solo puede pedir lo de sus casas (`csv_id_pau`): los ids de casa
 * (`id_ubi`, `id_cdc`, `sSeleccionados`), la selección de casas (`cdc_sel`: solo «una casa», con una suya)
 * y las actividades (`id_activ`, o `sel` al modificar una actividad del planning), que tienen que ser en
 * una de sus casas. Revisión de seguridad del 2026-11-18, punto 2 (comprobar el id). Antes las pantallas
 * solo le ofrecían sus casas, pero cambiando el parámetro llegaba a las de cualquier casa
 * (`cdc_sel=4`: todas las casas sv).
 *
 * Los parámetros que llegan dentro de una cápsula `HashB` no se miran: los ha firmado el servidor.
 */
final class AccesoRolPauIdsTest extends TestCase
{
    private const CASA = 'frontend/casas/controller/casa_actividades_lista.php';
    private const MIS_CASAS = ['101', '102'];

    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/orbix_pau_' . bin2hex(random_bytes(4));
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        rmdir($this->dir);
    }

    /** @param array<array-key, mixed> $parametros */
    private function ajeno(array $parametros, string $ruta = self::CASA, string $pau = 'cdc'): ?string
    {
        // Actividad 7 en la casa 101; la 8, en otra casa.
        $ubiDeActividad = static fn (int $id): ?int => [7 => 101, 8 => 999][$id] ?? null;

        return AccesoRolPau::parametroAjeno($pau, $ruta, $parametros, self::MIS_CASAS, $ubiDeActividad);
    }

    public function test_sus_casas_pasan(): void
    {
        $this->assertNull($this->ajeno(['id_ubi' => '101']));
        $this->assertNull($this->ajeno(['cdc_sel' => '9', 'id_cdc' => ['101', '102']]));
        $this->assertNull($this->ajeno(['cdc_sel' => '9', 'sSeleccionados' => '102']));
        $this->assertNull($this->ajeno(['id_ubi' => '', 'periodo' => 'actual']));
        $this->assertNull($this->ajeno(['id_activ' => '7']));
        $this->assertNull($this->ajeno(['id_activ' => '0']));
    }

    public function test_una_casa_ajena_no(): void
    {
        $this->assertSame('id_ubi', $this->ajeno(['id_ubi' => '555']));
        $this->assertSame('id_cdc', $this->ajeno(['cdc_sel' => '9', 'id_cdc' => ['101', '555']]));
        $this->assertSame('sSeleccionados', $this->ajeno(['cdc_sel' => '9', 'sSeleccionados' => '101,555']));
        $this->assertSame('id_ubi', $this->ajeno(['id_ubi' => '101 OR 1=1']));
    }

    /** `cdc_sel` distinto de 9 (todas las casas sv, sf, comunes…) o 9 sin ninguna casa: todas las casas. */
    public function test_un_grupo_de_casas_no(): void
    {
        $this->assertSame('cdc_sel', $this->ajeno(['cdc_sel' => '4']));
        $this->assertSame('cdc_sel', $this->ajeno(['cdc_sel' => '0', 'id_cdc' => ['101']]));
        $this->assertSame('cdc_sel', $this->ajeno(['cdc_sel' => '9']));
        $this->assertSame('cdc_sel', $this->ajeno(['cdc_sel' => '9', 'id_cdc' => ['']]));
    }

    public function test_una_actividad_de_otra_casa_no(): void
    {
        $this->assertSame('id_activ', $this->ajeno(['id_activ' => '8']));
        $this->assertSame('id_activ', $this->ajeno(['id_activ' => '9999']));
    }

    /** Al modificar una actividad desde el planning, la actividad llega en `sel[]` (`id_activ#…`). */
    public function test_la_actividad_del_planning_llega_en_sel(): void
    {
        $ruta = 'frontend/actividades/controller/planning_casa_modificar.php';
        $this->assertNull($this->ajeno(['sel' => ['7#1']], $ruta));
        $this->assertSame('sel', $this->ajeno(['sel' => ['8#1']], $ruta));
        // En otras pantallas `sel` no es una actividad.
        $this->assertNull($this->ajeno(['sel' => ['8#1']]));
    }

    /** Los demás pau y los usuarios de oficina no se miran aquí. */
    public function test_solo_se_mira_el_pau_de_casa(): void
    {
        $this->assertNull($this->ajeno(['id_ubi' => '555'], 'frontend/misas/controller/ver_plan_ctr.php', 'ctr'));
        $this->assertNull($this->ajeno(['cdc_sel' => '4'], self::CASA, ''));
    }

    public function test_en_modo_aviso_apunta_el_parametro_y_deja_pasar(): void
    {
        $permite = AccesoRolPau::decidir($this->dir, 'cdc', 'POST', '/orbix/' . self::CASA, ['cdc_sel' => '4'], false, self::MIS_CASAS, static fn (): ?int => null);

        $this->assertTrue($permite);
        $linea = (string) file_get_contents($this->dir . '/' . AccesoRolPau::REGISTRO_DENEGADO);
        $this->assertStringContainsString("cdc\tPOST\t" . self::CASA . "\tcdc_sel\tcdc_sel", $linea);
    }

    public function test_exigiendo_no_deja_pasar(): void
    {
        $this->assertFalse(AccesoRolPau::decidir($this->dir, 'cdc', 'POST', '/orbix/' . self::CASA, ['id_ubi' => '555'], true, self::MIS_CASAS, static fn (): ?int => null));
        $this->assertTrue(AccesoRolPau::decidir($this->dir, 'cdc', 'POST', '/orbix/' . self::CASA, ['id_ubi' => '101'], true, self::MIS_CASAS, static fn (): ?int => null));
    }
}
