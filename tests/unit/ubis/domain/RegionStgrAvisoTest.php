<?php

namespace tests\unit\ubis\domain;

use PHPUnit\Framework\TestCase;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;

final class RegionStgrAvisoTest extends TestCase
{
    public function test_es_dl_sin_region_reconoce_excepcion_de_configuracion(): void
    {
        $e = new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, 'L');
        $this->assertTrue(RegionStgrAviso::esDlSinRegion($e));
    }

    public function test_formatear_agrupa_dls_sin_duplicar_explicacion(): void
    {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, 'L'));
        RegionStgrAviso::registrar($problemas, new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, 'M'));
        RegionStgrAviso::registrar($problemas, new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, 'L'));

        $msg = RegionStgrAviso::formatear($problemas);
        $this->assertStringContainsString('«L»', $msg);
        $this->assertStringContainsString('«M»', $msg);
        $this->assertSame(1, substr_count($msg, 'Consecuencias'));
        $this->assertSame(1, substr_count($msg, 'Cómo corregirlo'));
    }

    public function test_registrar_no_duplica_la_misma_dl(): void
    {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, 'L'));
        RegionStgrAviso::registrar($problemas, new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, 'L'));

        $this->assertCount(1, $problemas[RegionStgrAviso::TIPO_DL_NO_ENCONTRADA]);
    }
}
