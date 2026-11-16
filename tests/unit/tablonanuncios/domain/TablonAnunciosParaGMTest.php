<?php

declare(strict_types=1);

namespace Tests\unit\tablonanuncios\domain;

use src\shared\domain\value_objects\DateTimeLocal;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\TablonAnunciosParaGM;
use Tests\myTest;

/** Datos de la tabla del tablón de anuncios de la portada (`public/portada.php` monta la `Lista`). */
final class TablonAnunciosParaGMTest extends myTest
{
    private function anuncio(string $uuid, string $texto, string $fecha): Anuncio
    {
        $a = new Anuncio();
        $a->setUuid_item($uuid);
        $a->setEsquemaEmisorVo('H-dlbv');
        $a->setTextoAnuncioVo($texto);
        $a->setT_anotado(new DateTimeLocal($fecha));

        return $a;
    }

    public function test_la_tabla_trae_cabeceras_boton_borrar_y_los_anuncios_por_fecha(): void
    {
        $repo = $this->createMock(AnuncioRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->anuncio('22222222-2222-4222-8222-222222222222', 'segundo', '2026-05-01 10:00:00'),
            $this->anuncio('11111111-1111-4111-8111-111111111111', 'primero', '2026-03-04 10:00:00'),
        ]);

        $tabla = (new TablonAnunciosParaGM($repo))->getTabla('ctr');

        $this->assertSame('tablon_anuncios', $tabla['id_tabla']);
        $this->assertCount(3, $tabla['cabeceras']);
        $this->assertSame('fnjs_borrar("#seleccionados")', $tabla['botones'][0]['click']);
        $this->assertSame(['primero', 'segundo'], array_column($tabla['datos'], 3));
        $this->assertSame(['H-dlbv', '4/3/2026'], [$tabla['datos'][0][1], $tabla['datos'][0][2]]);
        $this->assertNotSame('', $tabla['datos'][0]['sel']);
    }

    public function test_sin_anuncios_la_tabla_no_tiene_filas(): void
    {
        $repo = $this->createMock(AnuncioRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $this->assertSame([], (new TablonAnunciosParaGM($repo))->getTabla('ctr')['datos']);
    }
}
