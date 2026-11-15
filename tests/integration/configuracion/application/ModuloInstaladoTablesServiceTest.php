<?php

declare(strict_types=1);

namespace Tests\integration\configuracion\application;

use PDO;
use src\configuracion\application\ModuloInstaladoTablesService;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use Tests\myTest;

/**
 * Instalar y desinstalar las tablas de un módulo en la dl de la sesión (`ModuloInstaladoTablesService`
 * → `AppDB` → `src/<app>/db/DBEsquema`, sobre `DBAbstract`): DDL de verdad.
 *
 * Se usa el módulo «camas» (habitaciones), que la dl de los tests no tiene instalado: se crean sus
 * tablas (`du_habitaciones_dl`, `du_camas_dl`) y se vuelven a quitar. Solo en el entorno de pruebas
 * (`WEBDIR=pruebas`). Fase 6 del plan de revisión de tests (backlog «Tests del DDL de esquemas»).
 */
final class ModuloInstaladoTablesServiceTest extends myTest
{
    private const ID_MOD_CAMAS = 32;
    private const TABLAS = ['du_habitaciones_dl', 'du_camas_dl'];

    private ?PDO $comun = null;
    private string $esquema = '';
    private mixed $configAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        if (getenv('WEBDIR') !== 'pruebas') {
            $this->markTestSkipped('DDL de módulos: solo en el entorno de pruebas (tools/qa/phpunit_protegido.php).');
        }
        $this->comun = (new DBConnection((new ConfigDB('importar'))->getConexionMantenimiento('public')))->getPDO();
        $bd = $this->valor('SELECT current_database()');
        if (!str_starts_with($bd, 'pruebas-')) {
            $this->fail("La conexión de mantenimiento va a «{$bd}», no a una BD de pruebas: no se ejecuta DDL.");
        }
        $this->esquema = (string) preg_replace('/[vf]$/', '', ConfigGlobal::mi_region_dl());
        if ($this->tablas() !== []) {
            $this->esquema = '';
            $this->markTestSkipped('La dl de los tests ya tiene las tablas de habitaciones: el test no las toca.');
        }
        // `AppDB` busca el nombre de cada app del módulo en la configuración de la sesión.
        $this->configAnterior = $_SESSION['config'] ?? null;
        $_SESSION['config'] = ['a_apps' => ['asistentes' => 14, 'ubiscamas' => 36]] + (is_array($this->configAnterior) ? $this->configAnterior : []);
    }

    public function tearDown(): void
    {
        if ($this->comun !== null && $this->esquema !== '') {
            foreach (array_reverse(self::TABLAS) as $tabla) {
                $this->comun->exec('DROP TABLE IF EXISTS "' . $this->esquema . '".' . $tabla);
            }
            if ($this->configAnterior === null) {
                unset($_SESSION['config']);
            } else {
                $_SESSION['config'] = $this->configAnterior;
            }
        }
        parent::tearDown();
    }

    private function valor(string $sql): string
    {
        $st = $this->comun?->query($sql);
        $v = $st === null || $st === false ? false : $st->fetchColumn();

        return is_scalar($v) ? (string) $v : '';
    }

    /** @return list<string> tablas de habitaciones que tiene la dl */
    private function tablas(): array
    {
        $lista = $this->valor("SELECT coalesce(string_agg(tablename, ',' ORDER BY tablename), '') FROM pg_tables WHERE schemaname = '" . $this->esquema . "' AND tablename IN ('du_habitaciones_dl', 'du_camas_dl')");

        return $lista === '' ? [] : explode(',', $lista);
    }

    public function test_instalar_el_modulo_crea_sus_tablas_en_la_dl_y_desinstalarlo_las_quita(): void
    {
        $servicio = DependencyResolver::get(ModuloInstaladoTablesService::class);
        $this->assertInstanceOf(ModuloInstaladoTablesService::class, $servicio);

        $servicio->createTables(self::ID_MOD_CAMAS);

        $this->assertSame(['du_camas_dl', 'du_habitaciones_dl'], $this->tablas(), 'las dos tablas del módulo están en el esquema de la dl');
        foreach (self::TABLAS as $tabla) {
            $padre = $this->valor("SELECT p.relname FROM pg_inherits i JOIN pg_class c ON c.oid = i.inhrelid JOIN pg_namespace n ON n.oid = c.relnamespace JOIN pg_class p ON p.oid = i.inhparent WHERE n.nspname = '" . $this->esquema . "' AND c.relname = '$tabla'");
            $this->assertSame(substr($tabla, 0, -3), $padre, "$tabla hereda de la tabla global");
            $this->assertStringContainsString("idschema('" . $this->esquema . "'", $this->valor("SELECT column_default FROM information_schema.columns WHERE table_schema = '" . $this->esquema . "' AND table_name = '$tabla' AND column_name = 'id_schema'"));
            $this->assertSame($this->esquema, $this->valor("SELECT tableowner FROM pg_tables WHERE schemaname = '" . $this->esquema . "' AND tablename = '$tabla'"), "$tabla es del rol de la dl");
        }

        $servicio->dropTables(self::ID_MOD_CAMAS);

        $this->assertSame([], $this->tablas(), 'al desinstalar, las tablas del módulo desaparecen');
        $this->assertSame('1', $this->valor("SELECT count(*) FROM pg_tables WHERE schemaname = 'public' AND tablename = 'du_habitaciones'"), 'la tabla global no se toca');
    }
}
