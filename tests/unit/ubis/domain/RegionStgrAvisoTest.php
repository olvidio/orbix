<?php

namespace tests\unit\ubis\domain;

use PHPUnit\Framework\TestCase;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;

final class RegionStgrAvisoTest extends TestCase
{
    public function test_es_dl_sin_region_reconoce_excepcion_de_configuracion(): void
    {
        $e = new RegionStgrConfigException(RegionStgrAviso::mensajeDlNoEncontrada('L'));
        $this->assertTrue(RegionStgrAviso::esDlSinRegion($e));
    }

    public function test_mensaje_dl_no_encontrada_incluye_dl_consecuencias_y_correccion(): void
    {
        $msg = RegionStgrAviso::mensajeDlNoEncontrada('L');
        $this->assertStringContainsString('L', $msg);
        $this->assertStringContainsString('xu_dl', $msg);
        $this->assertStringContainsString('Consecuencias', $msg);
        $this->assertStringContainsString('Cómo corregirlo', $msg);
    }

    public function test_append_no_duplica_mensajes(): void
    {
        $uno = RegionStgrAviso::mensajeDlNoEncontrada('L');
        $this->assertSame($uno, RegionStgrAviso::append($uno, $uno));
    }
}
