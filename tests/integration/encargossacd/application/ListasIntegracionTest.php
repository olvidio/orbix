<?php

namespace Tests\integration\encargossacd\application;

use src\encargossacd\application\ListasAData;
use src\encargossacd\application\ListasBData;
use src\encargossacd\application\ListasCData;
use src\encargossacd\application\ListasComCtrData;
use src\encargossacd\application\ListasComSacdData;
use src\encargossacd\application\ListasComTxtData;
use src\encargossacd\application\ListasDData;
use src\encargossacd\application\ListasExigenciaCtrData;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * Los listados de encargos se montan con los repositorios y servicios reales. Cada uno se envía luego
 * como JSON (ContestarJson), así que además de la forma se comprueba que el resultado se serializa.
 */
class ListasIntegracionTest extends myTest
{
    private function usar(string $clase): object
    {
        return $GLOBALS['container']->get($clase);
    }

    /** @param array<string, mixed> $r */
    private function assertSerializable(array $r): void
    {
        $this->assertNotFalse(json_encode($r, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE));
    }

    /** @param array<string, mixed> $r */
    private function assertListaConCabeceras(array $r): void
    {
        $this->assertSame(['cabecera_left', 'cabecera_right', 'cabecera_right_2', 'Html'], array_keys($r));
        foreach ($r as $k => $v) {
            $this->assertIsString($v, $k);
        }
        $this->assertNotSame('', $r['cabecera_left']);
        $this->assertNotSame('', $r['cabecera_right']);
        $this->assertSerializable($r);
    }

    // ---- listas A, B, C, D --------------------------------------------------------------------------

    /**
     * @dataProvider seccionesProvider
     */
    public function test_lista_a(int $sf): void
    {
        $this->assertListaConCabeceras($this->usar(ListasAData::class)->execute($sf));
    }

    /**
     * @dataProvider seccionesProvider
     */
    public function test_lista_b(int $sf): void
    {
        $this->assertListaConCabeceras($this->usar(ListasBData::class)->execute($sf));
    }

    /**
     * @dataProvider seccionesProvider
     */
    public function test_lista_d(int $sf): void
    {
        $this->assertListaConCabeceras($this->usar(ListasDData::class)->execute($sf));
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function seccionesProvider(): iterable
    {
        yield 'sv' => [1];
        yield 'sf' => [2];
        yield 'sin indicar' => [0];
    }

    public function test_lista_c(): void
    {
        $this->assertListaConCabeceras($this->usar(ListasCData::class)->execute());
    }

    public function test_el_curso_de_la_cabecera_tiene_el_formato_esperado(): void
    {
        $r = $this->usar(ListasAData::class)->execute(1);

        $this->assertMatchesRegularExpression('/\d{1,2}[\/.]\d{1,2}[\/.]\d{2,4}.*\d{1,2}[\/.]\d{1,2}[\/.]\d{2,4}/', $r['cabecera_left']);
    }

    public function test_listas_sv_y_sf_dan_cabeceras_iguales_y_posiblemente_cuerpo_distinto(): void
    {
        $sv = $this->usar(ListasAData::class)->execute(1);
        $sf = $this->usar(ListasAData::class)->execute(2);

        $this->assertSame($sv['cabecera_left'], $sf['cabecera_left']);
        $this->assertSame($sv['cabecera_right'], $sf['cabecera_right']);
    }

    public function test_ejecutar_dos_veces_da_el_mismo_resultado(): void
    {
        $uc = $this->usar(ListasBData::class);

        $this->assertSame($uc->execute(1), $uc->execute(1), 'el caso de uso no guarda estado entre llamadas');
    }

    // ---- lista de exigencia del centro -----------------------------------------------------------------------

    /**
     * @dataProvider exigenciasProvider
     */
    public function test_exigencia_de_centros_e_iglesias(int $sf, string $ctrIgl): void
    {
        $this->assertListaConCabeceras($this->usar(ListasExigenciaCtrData::class)->execute($sf, $ctrIgl));
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function exigenciasProvider(): iterable
    {
        yield 'ctr sv' => [1, 'ctr'];
        yield 'ctr sf' => [2, 'ctr'];
        yield 'iglesias sv' => [1, 'igl'];
        yield 'sin tipo' => [1, ''];
        yield 'tipo desconocido' => [1, 'zzz'];
    }

    public function test_un_tipo_de_exigencia_desconocido_no_lista_centros(): void
    {
        $vacio = $this->usar(ListasExigenciaCtrData::class)->execute(1, 'zzz');
        $ctr = $this->usar(ListasExigenciaCtrData::class)->execute(1, 'ctr');

        $this->assertLessThanOrEqual(strlen($ctr['Html']), strlen($vacio['Html']));
    }

    // ---- comunicaciones -----------------------------------------------------------------------------------------

    /**
     * @dataProvider seleccionesDeSacdProvider
     */
    public function test_comunicacion_a_los_sacd(string $sel): void
    {
        $r = $this->usar(ListasComSacdData::class)->execute($sel);

        $this->assertSame(['array_modo', 'lugar_fecha'], array_keys($r));
        $this->assertIsArray($r['array_modo']);
        $this->assertIsString($r['lugar_fecha']);
        $this->assertStringContainsString(',', $r['lugar_fecha'], 'población, fecha');
        $this->assertSerializable($r);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function seleccionesDeSacdProvider(): iterable
    {
        yield 'numerarios y agregados' => ['nagd'];
        yield 'sssc' => ['sssc'];
        yield 'vacía' => [''];
        yield 'desconocida' => ['otra'];
    }

    public function test_una_seleccion_de_sacd_desconocida_no_incluye_a_nadie(): void
    {
        $this->assertSame([], $this->usar(ListasComSacdData::class)->execute('otra')['array_modo']);
        $this->assertSame([], $this->usar(ListasComSacdData::class)->execute('')['array_modo']);
    }

    public function test_cada_sacd_de_la_comunicacion_trae_nombre_textos_grupos_y_observaciones(): void
    {
        $modo = $this->usar(ListasComSacdData::class)->execute('nagd')['array_modo'];
        if ($modo === []) {
            $this->markTestSkipped('No hay sacd activos de la dl en la BD de pruebas.');
        }

        foreach ($modo as $sacd) {
            $this->assertArrayHasKey('nom_ap', $sacd);
            $this->assertArrayHasKey('txt', $sacd);
            $this->assertArrayHasKey('grupo', $sacd);
            $this->assertArrayHasKey('observ', $sacd);
            $this->assertNotSame('', $sacd['nom_ap']);
        }
    }

    /**
     * @dataProvider seccionesDeCentrosProvider
     */
    public function test_comunicacion_a_los_centros(string $sfsv): void
    {
        $r = $this->usar(ListasComCtrData::class)->execute($sfsv);

        $this->assertSame(['array_atn_sacd', 'origen_txt', 'lugar_fecha'], array_keys($r));
        $this->assertIsArray($r['array_atn_sacd']);
        $this->assertIsString($r['origen_txt']);
        $this->assertSerializable($r);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function seccionesDeCentrosProvider(): iterable
    {
        yield 'sv' => ['sv'];
        yield 'sf' => ['sf'];
        yield 'vacía' => [''];
    }

    public function test_texto_de_la_comunicacion(): void
    {
        $r = $this->usar(ListasComTxtData::class)->execute();

        $this->assertSame(['a_locales', 'texto_inicial', 'ctx_guardar'], array_keys($r));
        $this->assertIsArray($r['a_locales']);
        $this->assertNotEmpty($r['a_locales'], 'hay idiomas disponibles');
        $this->assertIsString($r['texto_inicial']);
        $this->assertSame(['clave' => 'com_sacd', 'idioma' => 'es'], HashB::open($r['ctx_guardar'], 'listas_com_txt_update'));
        $this->assertSerializable($r);
    }
}
