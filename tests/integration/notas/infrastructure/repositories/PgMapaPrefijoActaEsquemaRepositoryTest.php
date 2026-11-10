<?php

namespace Tests\integration\notas\infrastructure\repositories;

use PDO;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * `public.mapa_prefijo_acta_esquema` (BD comun) lleva datos reales: el test solo toca
 * filas con prefijo `tst…` (y esquemas `ZZ-…`) y las borra antes y después de cada caso.
 */
class PgMapaPrefijoActaEsquemaRepositoryTest extends myTest
{
    private MapaPrefijoActaEsquemaRepositoryInterface $repository;
    private PDO $pdo;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MapaPrefijoActaEsquemaRepositoryInterface::class);
        $this->pdo = GlobalPdo::get('oDBPC');
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $this->pdo->exec("DELETE FROM public.mapa_prefijo_acta_esquema WHERE pref LIKE 'tst%' OR esquema_base LIKE 'ZZ-%'");
    }

    /**
     * @return array{esquema_base: string, notas: ?string}|null
     */
    private function fila(string $pref): ?array
    {
        $stmt = $this->pdo->prepare('SELECT esquema_base, notas FROM public.mapa_prefijo_acta_esquema WHERE pref = :p');
        $stmt->execute(['p' => $pref]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : null;
    }

    // ---- esquemaBasePorPrefijo ------------------------------------------------

    public function test_esquema_base_por_prefijo_existente(): void
    {
        $this->repository->upsertPrefijo('tst1', 'ZZ-tstA');

        $this->assertSame('ZZ-tstA', $this->repository->esquemaBasePorPrefijo('tst1'));
    }

    public function test_esquema_base_por_prefijo_ignora_mayusculas_y_espacios(): void
    {
        $this->repository->upsertPrefijo('tst1', 'ZZ-tstA');

        $this->assertSame('ZZ-tstA', $this->repository->esquemaBasePorPrefijo('  TST1 '));
    }

    /**
     * @dataProvider prefijosSinResultadoProvider
     */
    public function test_esquema_base_por_prefijo_sin_resultado(string $prefijo): void
    {
        $this->assertNull($this->repository->esquemaBasePorPrefijo($prefijo));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function prefijosSinResultadoProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'solo espacios' => ['   '];
        yield 'inexistente' => ['tstnoexiste'];
        yield 'empieza por fin (acta de fin de ciclo)' => ['fin'];
        yield 'fin con sufijo' => ['fincuadrienio'];
        yield 'FIN en mayúsculas' => ['FIN'];
    }

    public function test_prefijo_que_empieza_por_fin_no_se_resuelve_aunque_este_en_el_mapa(): void
    {
        $this->pdo->exec("INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base) VALUES ('fintst', 'ZZ-tstfin')");
        try {
            $this->assertNull($this->repository->esquemaBasePorPrefijo('fintst'));
            $this->assertNull($this->repository->esquemaDestinoDesdeActa('fintst 1/24', 'v'));
        } finally {
            $this->pdo->exec("DELETE FROM public.mapa_prefijo_acta_esquema WHERE pref = 'fintst'");
        }
    }

    public function test_prefijo_con_fin_en_medio_si_se_resuelve(): void
    {
        $this->pdo->exec("INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base) VALUES ('tstfin', 'ZZ-tstfin')");

        $this->assertSame('ZZ-tstfin', $this->repository->esquemaBasePorPrefijo('tstfin'));
    }

    // ---- esquemaDestinoDesdeActa ------------------------------------------------

    public function test_esquema_destino_desde_acta_anade_el_sufijo(): void
    {
        $this->repository->upsertPrefijo('tst1', 'ZZ-tstA');

        $this->assertSame('ZZ-tstAv', $this->repository->esquemaDestinoDesdeActa('tst1 12/24', 'v'));
        $this->assertSame('ZZ-tstAf', $this->repository->esquemaDestinoDesdeActa('tst1 12/24', 'f'));
    }

    public function test_esquema_destino_usa_solo_la_primera_palabra_del_acta(): void
    {
        $this->repository->upsertPrefijo('tst1', 'ZZ-tstA');

        $this->assertSame('ZZ-tstAv', $this->repository->esquemaDestinoDesdeActa('  TST1   nº 5 bis', 'v'));
        $this->assertSame('ZZ-tstAv', $this->repository->esquemaDestinoDesdeActa('tst1', 'v'));
    }

    public function test_esquema_destino_acta_vacia_o_prefijo_desconocido_es_null(): void
    {
        $this->assertNull($this->repository->esquemaDestinoDesdeActa('', 'v'));
        $this->assertNull($this->repository->esquemaDestinoDesdeActa('   ', 'f'));
        $this->assertNull($this->repository->esquemaDestinoDesdeActa('tstnoexiste 1/24', 'v'));
    }

    /**
     * @dataProvider sufijosInvalidosProvider
     */
    public function test_esquema_destino_sufijo_invalido_lanza_excepcion(string $suffix): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repository->esquemaDestinoDesdeActa('tst1 1/24', $suffix);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function sufijosInvalidosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'mayúscula' => ['V'];
        yield 'otra letra' => ['x'];
        yield 'dos letras' => ['vf'];
    }

    // ---- prefijosPorEsquemaBase / prefijoPerteneceAEsquema --------------------------

    public function test_prefijos_por_esquema_base_ordenados(): void
    {
        $this->repository->upsertPrefijo('tstc', 'ZZ-tstB');
        $this->repository->upsertPrefijo('tsta', 'ZZ-tstB');
        $this->repository->upsertPrefijo('tstb', 'ZZ-tstB');
        $this->repository->upsertPrefijo('tstd', 'ZZ-tstOtro');

        $this->assertSame(['tsta', 'tstb', 'tstc'], $this->repository->prefijosPorEsquemaBase('ZZ-tstB'));
    }

    public function test_prefijos_por_esquema_base_vacio_o_desconocido(): void
    {
        $this->assertSame([], $this->repository->prefijosPorEsquemaBase(''));
        $this->assertSame([], $this->repository->prefijosPorEsquemaBase('  '));
        $this->assertSame([], $this->repository->prefijosPorEsquemaBase('ZZ-noexiste'));
    }

    public function test_prefijo_pertenece_a_esquema(): void
    {
        $this->repository->upsertPrefijo('tst1', 'ZZ-tstA');

        $this->assertTrue($this->repository->prefijoPerteneceAEsquema('tst1', 'ZZ-tstA'));
        $this->assertTrue($this->repository->prefijoPerteneceAEsquema('TST1', ' zz-TSTa '), 'sin distinguir mayúsculas ni espacios');
        $this->assertFalse($this->repository->prefijoPerteneceAEsquema('tst1', 'ZZ-tstOtro'));
        $this->assertFalse($this->repository->prefijoPerteneceAEsquema('tstnoexiste', 'ZZ-tstA'));
        $this->assertFalse($this->repository->prefijoPerteneceAEsquema('fin', 'ZZ-tstA'));
    }

    // ---- upsertPrefijo ---------------------------------------------------------------

    public function test_upsert_inserta_en_minusculas_y_recorta(): void
    {
        $this->repository->upsertPrefijo('  TSTU ', '  ZZ-tstU ', 'nota inicial');

        $fila = $this->fila('tstu');
        $this->assertNotNull($fila);
        $this->assertSame('ZZ-tstU', $fila['esquema_base']);
        $this->assertSame('nota inicial', $fila['notas']);
    }

    public function test_upsert_existente_actualiza_el_esquema_y_conserva_las_notas_si_no_se_pasan(): void
    {
        $this->repository->upsertPrefijo('tstu', 'ZZ-tstU', 'nota original');

        $this->repository->upsertPrefijo('tstu', 'ZZ-tstV');

        $fila = $this->fila('tstu');
        $this->assertSame('ZZ-tstV', $fila['esquema_base']);
        $this->assertSame('nota original', $fila['notas']);
    }

    public function test_upsert_existente_con_notas_las_reemplaza(): void
    {
        $this->repository->upsertPrefijo('tstu', 'ZZ-tstU', 'nota original');

        $this->repository->upsertPrefijo('tstu', 'ZZ-tstU', 'nota nueva');

        $this->assertSame('nota nueva', $this->fila('tstu')['notas']);
    }

    public function test_upsert_sin_notas_deja_notas_null(): void
    {
        $this->repository->upsertPrefijo('tstu', 'ZZ-tstU');

        $this->assertNull($this->fila('tstu')['notas']);
    }

    public function test_upsert_con_prefijo_o_esquema_vacio_no_hace_nada(): void
    {
        $this->repository->upsertPrefijo('', 'ZZ-tstU');
        $this->repository->upsertPrefijo('   ', 'ZZ-tstU');
        $this->repository->upsertPrefijo('tstu', '');
        $this->repository->upsertPrefijo('tstu', '   ');

        $this->assertNull($this->fila('tstu'));
        $this->assertSame([], $this->repository->prefijosPorEsquemaBase('ZZ-tstU'));
    }

    // ---- reasignarEsquemaBase ---------------------------------------------------------

    public function test_reasignar_mueve_todos_los_prefijos_del_esquema_anterior(): void
    {
        $this->repository->upsertPrefijo('tsta', 'ZZ-tstOld');
        $this->repository->upsertPrefijo('tstb', 'ZZ-tstOld');
        $this->repository->upsertPrefijo('tstc', 'ZZ-tstOtro');

        $this->repository->reasignarEsquemaBase('ZZ-tstOld', 'ZZ-tstNew');

        $this->assertSame(['tsta', 'tstb'], $this->repository->prefijosPorEsquemaBase('ZZ-tstNew'));
        $this->assertSame([], $this->repository->prefijosPorEsquemaBase('ZZ-tstOld'));
        $this->assertSame('ZZ-tstOtro', $this->repository->esquemaBasePorPrefijo('tstc'));
    }

    /**
     * @dataProvider reasignacionesSinEfectoProvider
     */
    public function test_reasignar_sin_efecto(string $viejo, string $nuevo): void
    {
        $this->repository->upsertPrefijo('tsta', 'ZZ-tstOld');

        $this->repository->reasignarEsquemaBase($viejo, $nuevo);

        $this->assertSame('ZZ-tstOld', $this->repository->esquemaBasePorPrefijo('tsta'));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function reasignacionesSinEfectoProvider(): iterable
    {
        yield 'anterior vacío' => ['', 'ZZ-tstNew'];
        yield 'nuevo vacío' => ['ZZ-tstOld', ''];
        yield 'mismo esquema' => ['ZZ-tstOld', 'ZZ-tstOld'];
        yield 'mismo esquema sin distinguir mayúsculas' => ['ZZ-tstOld', 'zz-TSTOLD'];
    }

    // ---- fusionesEsquemaBase ----------------------------------------------------------

    public function test_fusiones_derivan_origen_y_destino(): void
    {
        $this->repository->upsertPrefijo('tstz', 'ZZ-tstBase', 'fusionada en tstbase');

        $fusiones = $this->repository->fusionesEsquemaBase();

        $this->assertSame('ZZ-tstBase', $fusiones['ZZ-tstz'] ?? null);
    }

    public function test_fusiones_ignora_filas_sin_la_palabra_fusion(): void
    {
        $this->repository->upsertPrefijo('tstz', 'ZZ-tstBase', 'otra nota');
        $this->repository->upsertPrefijo('tsty', 'ZZ-tstBase');

        $fusiones = $this->repository->fusionesEsquemaBase();

        $this->assertArrayNotHasKey('ZZ-tstz', $fusiones);
        $this->assertArrayNotHasKey('ZZ-tsty', $fusiones);
    }

    public function test_fusiones_es_insensible_a_mayusculas_en_las_notas(): void
    {
        $this->repository->upsertPrefijo('tstz', 'ZZ-tstBase', 'FUSIONADA en tstbase');

        $this->assertArrayHasKey('ZZ-tstz', $this->repository->fusionesEsquemaBase());
    }

    public function test_fusiones_ignora_el_esquema_que_apunta_a_si_mismo(): void
    {
        $this->repository->upsertPrefijo('tstz', 'ZZ-tstz', 'fusionada consigo misma');

        $this->assertArrayNotHasKey('ZZ-tstz', $this->repository->fusionesEsquemaBase());
    }

    public function test_fusiones_ignora_esquemas_sin_guion(): void
    {
        $this->pdo->exec("INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas) VALUES ('tstw', 'ZZnoguion', 'fusionada')");

        $fusiones = $this->repository->fusionesEsquemaBase();

        $this->assertNotContains('ZZnoguion', $fusiones);
        $this->limpiarSinGuion();
    }

    private function limpiarSinGuion(): void
    {
        $this->pdo->exec("DELETE FROM public.mapa_prefijo_acta_esquema WHERE esquema_base = 'ZZnoguion'");
    }

    public function test_fusiones_incluye_las_reales_del_mapa(): void
    {
        $fusiones = $this->repository->fusionesEsquemaBase();

        foreach ($fusiones as $origen => $destino) {
            $this->assertStringContainsString('-', $origen);
            $this->assertStringContainsString('-', $destino);
            $this->assertNotSame(strtolower($origen), strtolower($destino));
        }
    }
}
