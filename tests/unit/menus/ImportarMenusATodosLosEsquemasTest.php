<?php

declare(strict_types=1);

namespace Tests\unit\menus;

use PHPUnit\Framework\TestCase;

/**
 * Importar los menús vacía y rellena las tablas de menús de un esquema. Hacerlo en todos los esquemas es
 * una herramienta de administración de la base de datos: pide ese permiso, la cápsula llega en el cuerpo
 * y cada esquema se cambia entero o no se cambia (revisión de seguridad del 2026-11-20, LOG-09).
 */
final class ImportarMenusATodosLosEsquemasTest extends TestCase
{
    private function codigo(): string
    {
        return (string) file_get_contents(dirname(__DIR__, 3) . '/src/menus/infrastructure/ui/http/controllers/menus_importar_de_ficheros_a_ref.php');
    }

    public function test_todos_los_esquemas_pide_el_permiso_de_herramientas_de_bd(): void
    {
        $this->assertMatchesRegularExpression('/\$Qtodos === 1 && !PermisoHerramientasBdGuard::permiteEsquemas\(\)/', $this->codigo());
    }

    public function test_la_capsula_no_se_admite_por_la_query(): void
    {
        $this->assertStringNotContainsString("inputString(\$_GET, 'ctx_importar')", $this->codigo());
    }

    public function test_cada_esquema_se_importa_dentro_de_una_transaccion(): void
    {
        $codigo = $this->codigo();

        $this->assertSame(1, substr_count($codigo, '$oDB->beginTransaction();'));
        $this->assertSame(1, substr_count($codigo, '$oDB->commit();'));
        $this->assertLessThan(strpos($codigo, 'TRUNCATE TABLE aux_grupmenu '), strpos($codigo, '$oDB->beginTransaction();'));
        $this->assertGreaterThan(strrpos($codigo, 'TRUNCATE TABLE'), strpos($codigo, '$oDB->commit();'));
    }
}
