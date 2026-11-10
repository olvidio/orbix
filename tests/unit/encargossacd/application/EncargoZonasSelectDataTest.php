<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoZonasSelectData;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class EncargoZonasSelectDataTest extends TestCase
{
    public function test_contrato_del_desplegable_de_zonas(): void
    {
        $repo = $this->createMock(ZonaRepositoryInterface::class);
        $repo->expects($this->once())->method('getArrayZonas')->willReturn([30 => 'Zona 30', 7 => 'Zona 7']);

        $out = (new EncargoZonasSelectData($repo))->execute(7);

        $this->assertSame('id_zona_sel', $out['id']);
        $this->assertSame('id_zona_sel', $out['name']);
        $this->assertSame([['30', 'Zona 30'], ['7', 'Zona 7']], $out['opciones'], 'pares en el orden del repositorio');
        $this->assertSame('7', $out['selected']);
        $this->assertFalse($out['blanco']);
        $this->assertSame('fnjs_lista_ctrs_por_zona()', $out['action']);
        $this->assertStringEndsWith(': ', $out['label_prefix']);
    }

    public function test_sin_zona_seleccionada_el_valor_es_cero(): void
    {
        $repo = $this->createMock(ZonaRepositoryInterface::class);
        $repo->method('getArrayZonas')->willReturn([]);

        $out = (new EncargoZonasSelectData($repo))->execute(0);

        $this->assertSame('0', $out['selected']);
        $this->assertSame([], $out['opciones']);
    }
}
