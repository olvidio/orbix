<?php

declare(strict_types=1);

namespace Tests\unit\frontend\helpers;

use frontend\shared\helpers\ManualAnclas;
use PHPUnit\Framework\TestCase;

final class ManualAnclasTest extends TestCase
{
    public function test_slug_quita_acentos_y_signos(): void
    {
        self::assertSame('cambiar-el-tipo-de-una-actividad', ManualAnclas::slug('Cambiar el tipo de una actividad'));
        self::assertSame('errores-o-avisos-frecuentes', ManualAnclas::slug('Errores O Avisos Frecuentes'));
        self::assertSame('ver-si-una-fase-esta-completada', ManualAnclas::slug('Ver si una fase está completada'));
        self::assertSame('que-es-la-dl', ManualAnclas::slug('¿Qué es la <code>dl</code>?'));
    }

    public function test_slug_vacio_devuelve_seccion(): void
    {
        self::assertSame('seccion', ManualAnclas::slug('¿?'));
    }

    public function test_procesar_anade_id_a_los_encabezados(): void
    {
        $r = ManualAnclas::procesar('<h2>Crear una actividad</h2><p>x</p><h3>Para Que Sirve</h3>');

        self::assertSame(
            '<h2 id="crear-una-actividad">Crear una actividad</h2><p>x</p><h3 id="para-que-sirve">Para Que Sirve</h3>',
            $r['html'],
        );
        self::assertSame(
            [
                ['nivel' => 2, 'texto' => 'Crear una actividad', 'id' => 'crear-una-actividad'],
                ['nivel' => 3, 'texto' => 'Para Que Sirve', 'id' => 'para-que-sirve'],
            ],
            $r['encabezados'],
        );
    }

    public function test_procesar_desambigua_repetidos_en_orden_de_aparicion(): void
    {
        $r = ManualAnclas::procesar('<h2>A</h2><h3>Tareas</h3><h2>B</h2><h3>Tareas</h3><h4>A</h4>');

        self::assertSame(
            ['a', 'tareas', 'b', 'tareas-2', 'a-2'],
            array_column($r['encabezados'], 'id'),
        );
    }

    public function test_procesar_respeta_id_existente(): void
    {
        $r = ManualAnclas::procesar('<h2 id="mio">Hola</h2><h2>Mio</h2>');

        self::assertSame('<h2 id="mio">Hola</h2><h2 id="mio-2">Mio</h2>', $r['html']);
    }

    public function test_procesar_sin_encabezados_deja_el_html_igual(): void
    {
        $r = ManualAnclas::procesar('<p>nada</p>');

        self::assertSame('<p>nada</p>', $r['html']);
        self::assertSame([], $r['encabezados']);
    }
}
