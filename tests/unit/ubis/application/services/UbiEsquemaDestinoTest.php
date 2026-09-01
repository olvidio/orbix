<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application\services;

use PHPUnit\Framework\TestCase;
use src\ubis\application\services\UbiEsquemaDestino;

final class UbiEsquemaDestinoTest extends TestCase
{
    public function test_normalizar_region_y_dl(): void
    {
        $out = UbiEsquemaDestino::normalizar('H', 'dlmE');
        $this->assertSame('H', $out['region']);
        $this->assertSame('dlmE', $out['dl']);
        $this->assertSame('H-dlmE', $out['esquema']);
    }

    public function test_normalizar_esquema_en_dl(): void
    {
        $out = UbiEsquemaDestino::normalizar('Li', 'H-dlmE');
        $this->assertSame('H', $out['region']);
        $this->assertSame('dlmE', $out['dl']);
        $this->assertSame('H-dlmE', $out['esquema']);
    }

    public function test_normalizar_esquema_en_region(): void
    {
        $out = UbiEsquemaDestino::normalizar('H-dlmE', '');
        $this->assertSame('H', $out['region']);
        $this->assertSame('dlmE', $out['dl']);
        $this->assertSame('H-dlmE', $out['esquema']);
    }

    public function test_sin_datos_esquema_vacio(): void
    {
        $out = UbiEsquemaDestino::normalizar('', '');
        $this->assertSame('', $out['esquema']);
    }

    public function test_identificador_valido(): void
    {
        $this->assertTrue(UbiEsquemaDestino::esIdentificadorValido('H-dlmE'));
        $this->assertTrue(UbiEsquemaDestino::pareceEsquema('H-dlmE'));
        $this->assertFalse(UbiEsquemaDestino::esIdentificadorValido('H;drop'));
        $this->assertFalse(UbiEsquemaDestino::esIdentificadorValido(''));
        $this->assertFalse(UbiEsquemaDestino::pareceEsquema('dlmE'));
    }
}
