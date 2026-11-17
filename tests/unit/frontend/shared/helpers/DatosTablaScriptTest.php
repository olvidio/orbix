<?php

declare(strict_types=1);

namespace Tests\unit\frontend\shared\helpers;

use frontend\shared\helpers\DatosTablaScript;
use PHPUnit\Framework\TestCase;

/**
 * El script de las pantallas genéricas de tabla sale igual que el que montaba
 * `src\shared\domain\DatosTablaRepo::getScript()` (capturado en `fixtures/`).
 */
final class DatosTablaScriptTest extends TestCase
{
    private static function fixture(string $nombre): string
    {
        return (string) file_get_contents(__DIR__ . '/fixtures/' . $nombre);
    }

    public function test_valores_por_defecto_como_tabladb_lista(): void
    {
        $this->assertSame(self::fixture('datos_tabla_script_defecto.js'), DatosTablaScript::script('¿Quitar este cargo?'));
    }

    public function test_bloque_y_acciones_de_un_dossier(): void
    {
        $this->assertSame(
            self::fixture('datos_tabla_script_dossier.js'),
            DatosTablaScript::script('', '#ficha', 'frontend/x/form.php', 'src/x/update', 'https://h/frontend/x.php?h=1')
        );
    }
}
