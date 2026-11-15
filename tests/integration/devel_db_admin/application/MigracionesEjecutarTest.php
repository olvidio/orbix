<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\application;

use PDO;
use src\devel_db_admin\application\MigracionesEjecutar;
use src\devel_db_admin\application\MigracionesEscanear;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;

/**
 * Ejecución real de migraciones SQL (`MigracionesEjecutar`) con una carpeta temporal de migraciones
 * de prueba: estructura en comun, datos en comun, el comodín `*.` sobre todos los esquemas de sv y una
 * migración que falla.
 *
 * Solo en el entorno de pruebas (`WEBDIR=pruebas`): crea tablas `zz_test_migracion*` y filas en el
 * registro `migracion_aplicada` con prefijo 2099…, y las borra al acabar. Fase 6 del plan de revisión
 * de tests (backlog «Tests del DDL de esquemas»).
 */
final class MigracionesEjecutarTest extends myTest
{
    private const PREFIJO = '20990101';

    private string $dir = '';

    public function setUp(): void
    {
        parent::setUp();
        if (getenv('WEBDIR') !== 'pruebas') {
            $this->markTestSkipped('Migraciones reales: solo en el entorno de pruebas (tools/qa/phpunit_protegido.php).');
        }
        foreach (['oDBPC', 'oDB'] as $g) {
            $bd = $this->valor($g, 'SELECT current_database()');
            if (!str_starts_with($bd, 'pruebas-')) {
                $this->fail("La conexión «{$g}» va a «{$bd}», no a una BD de pruebas: no se ejecutan migraciones.");
            }
        }
        $this->dir = sys_get_temp_dir() . '/orbix_migraciones_test_' . getmypid();
        @mkdir($this->dir, 0700, true);
        $this->limpiar();
    }

    public function tearDown(): void
    {
        if ($this->dir !== '') {
            $this->limpiar();
            array_map('unlink', glob($this->dir . '/*') ?: []);
            @rmdir($this->dir);
        }
        parent::tearDown();
    }

    /** @var array<string, PDO> */
    private array $conexiones = [];

    /**
     * Conexión de mantenimiento a comun (`oDBPC`) o a sv (`oDB`): las migraciones se ejecutan con ese
     * usuario y las tablas que crean son suyas (el usuario de la aplicación no puede leerlas ni borrarlas).
     */
    private function pdo(string $global): PDO
    {
        return $this->conexiones[$global] ??= (new DBConnection((new ConfigDB('importar'))->getConexionMantenimiento($global === 'oDB' ? 'publicv' : 'public')))->getPDO();
    }

    private function valor(string $global, string $sql): string
    {
        $st = $this->pdo($global)->query($sql);
        $v = $st === false ? false : $st->fetchColumn();

        return is_scalar($v) ? (string) $v : '';
    }

    private function limpiar(): void
    {
        $comun = $this->pdo('oDBPC');
        $sv = $this->pdo('oDB');
        $comun->exec('DROP TABLE IF EXISTS public.zz_test_migracion');
        $comun->exec("DELETE FROM public.migracion_aplicada WHERE prefijo LIKE '" . self::PREFIJO . "%'");
        $st = $sv->query("SELECT schemaname FROM pg_tables WHERE tablename = 'zz_test_migracion_dl'");
        foreach ($st === false ? [] : $st->fetchAll(PDO::FETCH_COLUMN) as $esquema) {
            $sv->exec('DROP TABLE IF EXISTS "' . $esquema . '".zz_test_migracion_dl');
        }
    }

    private function migracion(string $numero, string $descripcion, string $db, string $sql): void
    {
        file_put_contents(sprintf('%s/%s%s_%s__%s.sql', $this->dir, self::PREFIJO, $numero, $descripcion, $db), $sql . "\n");
    }

    private function ejecutor(): MigracionesEjecutar
    {
        $registro = DependencyResolver::get(MigracionAplicadaRepositoryInterface::class);
        $esquemas = DependencyResolver::get(DbSchemaRepositoryInterface::class);
        $this->assertInstanceOf(MigracionAplicadaRepositoryInterface::class, $registro);
        $this->assertInstanceOf(DbSchemaRepositoryInterface::class, $esquemas);

        return new MigracionesEjecutar($registro, $esquemas, $this->dir);
    }

    /** @return list<string> ids de las migraciones de la carpeta de prueba, en orden */
    private function ids(): array
    {
        $registro = DependencyResolver::get(MigracionAplicadaRepositoryInterface::class);
        $this->assertInstanceOf(MigracionAplicadaRepositoryInterface::class, $registro);
        $ids = [];
        foreach ((new MigracionesEscanear($registro, $this->dir))->escanear()['migraciones'] as $m) {
            $ids[] = is_scalar($m['id'] ?? null) ? (string) $m['id'] : '';
        }
        sort($ids, SORT_STRING);

        return $ids;
    }

    /** @return array<string, string> «prefijo_descripcion@bd» => ok ('t' / 'f') */
    private function registro(): array
    {
        $st = $this->pdo('oDBPC')->query("SELECT prefijo || '_' || descripcion || '@' || database, ok::text FROM public.migracion_aplicada WHERE prefijo LIKE '" . self::PREFIJO . "%' ORDER BY 1");
        $out = [];
        foreach ($st === false ? [] : $st->fetchAll(PDO::FETCH_NUM) as [$clave, $ok]) {
            $out[(string) $clave] = $ok === 'true' ? 't' : 'f';
        }

        return $out;
    }

    public function test_estructura_datos_y_comodin_de_esquema_se_aplican_y_quedan_registrados(): void
    {
        $this->migracion('0001', 'zz_test_tabla', 'comun', 'CREATE TABLE public.zz_test_migracion (id integer PRIMARY KEY, texto text);');
        $this->migracion('0002', 'zz_test_datos', 'comun', "INSERT INTO public.zz_test_migracion VALUES (1, 'uno'), (2, 'dos');");
        $this->migracion('0003', 'zz_test_comodin', 'sv', 'CREATE TABLE *.zz_test_migracion_dl (id integer);');

        $r = $this->ejecutor()->ejecutar('seleccion', $this->ids());

        $this->assertNull($r['error'], implode("\n", $r['lines']));
        $this->assertSame('uno,dos', $this->valor('oDBPC', "SELECT string_agg(texto, ',' ORDER BY id) FROM public.zz_test_migracion"));
        // El comodín `*.` crea la tabla en todos los esquemas de sv registrados.
        $esperados = (int) $this->valor('oDBPC', "SELECT count(*) FROM public.db_idschema d WHERE schema LIKE '%v' AND id BETWEEN 1000 AND 1999");
        $creadas = (int) $this->valor('oDB', "SELECT count(*) FROM pg_tables WHERE tablename = 'zz_test_migracion_dl'");
        $this->assertGreaterThan(1, $creadas, 'la tabla está en varios esquemas de sv');
        $this->assertSame('1', $this->valor('oDB', "SELECT count(*) FROM pg_tables WHERE tablename = 'zz_test_migracion_dl' AND schemaname = 'H-dlbv'"));
        $this->assertLessThanOrEqual($esperados, $creadas);

        $registro = $this->registro();
        $this->assertSame('t', $registro[self::PREFIJO . '0001_zz_test_tabla@comun'] ?? null, (string) json_encode($registro));
        $this->assertSame('t', $registro[self::PREFIJO . '0002_zz_test_datos@comun'] ?? null);
        $this->assertSame('t', $registro[self::PREFIJO . '0003_zz_test_comodin@sv'] ?? null);
    }

    public function test_una_migracion_que_falla_se_registra_como_fallida_y_detiene_las_siguientes(): void
    {
        $this->migracion('0001', 'zz_test_tabla', 'comun', 'CREATE TABLE public.zz_test_migracion (id integer PRIMARY KEY, texto text);');
        $this->migracion('0002', 'zz_test_error', 'comun', 'ALTER TABLE public.zz_tabla_que_no_existe ADD COLUMN x integer;');
        $this->migracion('0003', 'zz_test_datos', 'comun', "INSERT INTO public.zz_test_migracion VALUES (1, 'uno');");

        $r = $this->ejecutor()->ejecutar('seleccion', $this->ids());

        $this->assertNotNull($r['error'], 'la ejecución informa del error');
        $this->assertStringContainsString('zz_tabla_que_no_existe', (string) $r['error'] . implode("\n", $r['lines']));
        $this->assertSame('0', $this->valor('oDBPC', 'SELECT count(*) FROM public.zz_test_migracion'), 'la migración posterior a la que falla no se ejecuta');
        $registro = $this->registro();
        $this->assertSame('t', $registro[self::PREFIJO . '0001_zz_test_tabla@comun'] ?? null, (string) json_encode($registro));
        $this->assertSame('f', $registro[self::PREFIJO . '0002_zz_test_error@comun'] ?? null, 'la que falla queda anotada como no aplicada');
        $this->assertArrayNotHasKey(self::PREFIJO . '0003_zz_test_datos@comun', $registro);
    }

    public function test_hasta_un_prefijo_ejecuta_solo_las_anteriores_y_no_repite_las_ya_aplicadas(): void
    {
        $this->migracion('0001', 'zz_test_tabla', 'comun', 'CREATE TABLE public.zz_test_migracion (id integer PRIMARY KEY, texto text);');
        $this->migracion('0002', 'zz_test_datos', 'comun', "INSERT INTO public.zz_test_migracion VALUES (1, 'uno');");
        $this->migracion('0003', 'zz_test_mas_datos', 'comun', "INSERT INTO public.zz_test_migracion VALUES (2, 'dos');");
        $ids = $this->ids();

        $r = $this->ejecutor()->ejecutar('hasta', [], $ids[1]);
        $this->assertNull($r['error'], implode("\n", $r['lines']));
        $this->assertSame('1', $this->valor('oDBPC', 'SELECT count(*) FROM public.zz_test_migracion'), 'solo hasta la segunda');

        // Otra vez hasta la tercera: las dos primeras ya están aplicadas y no se repiten (la tabla ya existe).
        $r = $this->ejecutor()->ejecutar('hasta', [], $ids[2]);
        $this->assertNull($r['error'], implode("\n", $r['lines']));
        $this->assertSame('uno,dos', $this->valor('oDBPC', "SELECT string_agg(texto, ',' ORDER BY id) FROM public.zz_test_migracion"));
    }
}
