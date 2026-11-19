<?php

declare(strict_types=1);

namespace Tests\unit\utils_database\domain\entity;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\shared\config\ServerConf;
use src\utils_database\domain\entity\DBAbstract;

/**
 * Lógica de `DBAbstract` que no necesita DDL real: nombre de tabla, permiso efectivo en la réplica, `eliminar`
 * (CASCADE solo en modo debug), transacción de `executeSql`, `tableExists` y los atajos sin rol. Con un PDO
 * simulado que anota lo que se ejecuta. El DDL de verdad lo ejercen `ModuloInstaladoTablesServiceTest` y
 * `EsquemaCicloDeVidaTest` (devel_db_admin).
 */
final class DBAbstractLogicaTest extends TestCase
{
    private bool $debugAnterior;
    private mixed $gestorErroresAnterior = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->debugAnterior = ServerConf::$debug;
        // Sin gestor de errores en la sesión: con el PDO simulado no hay error de PostgreSQL que anotar.
        $this->gestorErroresAnterior = $_SESSION['oGestorErrores'] ?? null;
        unset($_SESSION['oGestorErrores']);
    }

    protected function tearDown(): void
    {
        ServerConf::$debug = $this->debugAnterior;
        if ($this->gestorErroresAnterior !== null) {
            $_SESSION['oGestorErrores'] = $this->gestorErroresAnterior;
        }
        parent::tearDown();
    }

    /**
     * PDO simulado: anota `exec`, `beginTransaction`, `commit` y `rollback`; `exec` falla en la llamada indicada.
     *
     * @param list<string> $log
     */
    private function pdo(array &$log, ?int $fallaEnExec = null): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $n = 0;
        $pdo->method('beginTransaction')->willReturnCallback(function () use (&$log) {
            $log[] = 'BEGIN';

            return true;
        });
        $pdo->method('commit')->willReturnCallback(function () use (&$log) {
            $log[] = 'COMMIT';

            return true;
        });
        $pdo->method('rollBack')->willReturnCallback(function () use (&$log) {
            $log[] = 'ROLLBACK';

            return true;
        });
        $pdo->method('exec')->willReturnCallback(function (string $sql) use (&$log, &$n, $fallaEnExec) {
            $n++;
            $log[] = $sql;

            return $n === $fallaEnExec ? false : 0;
        });

        return $pdo;
    }

    private function db(?PDO $pdo, string $esquema = 'H-dlb', string $vf = '', string $role = '', string $roleVf = ''): DBAbstractDoble
    {
        return new DBAbstractDoble($pdo, $esquema, $vf, $role, $roleVf);
    }

    /**
     * Copiar desde un fichero necesita una conexión `Pdo\Pgsql`: sobre un `PDO` genérico el método del driver
     * (`pgsqlCopyFromFile`) está obsoleto en PHP 8.5, así que no se usa como alternativa.
     */
    public function test_copiar_desde_fichero_sin_conexion_pdo_pgsql_falla_con_un_mensaje(): void
    {
        foreach ([null, $this->createMock(PDO::class)] as $pdo) {
            try {
                $this->db($pdo)->copiar('t', '/no/importa');
                $this->fail('debía fallar con ' . get_debug_type($pdo));
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('Pdo\\Pgsql', $e->getMessage());
            }
        }
    }

    public function test_nombre_de_tabla_con_el_esquema_entre_comillas_y_public_con_su_sufijo(): void
    {
        $this->assertSame('"H-dlb".d_zonas', $this->db(null)->nomTabla('d_zonas'));
        $this->assertSame('"publicv".d_zonas', $this->db(null, 'public', 'v')->nomTabla('d_zonas'));
        $this->assertSame('"public".d_zonas', $this->db(null, 'public')->nomTabla('d_zonas'));
    }

    public function test_permiso_efectivo_solo_cambia_mientras_se_opera_en_la_replica(): void
    {
        $db = $this->db(null);
        foreach (['comun', 'sfsv', 'sfsv-e', 'sfsv-d', 'otro'] as $permiso) {
            $this->assertSame($permiso, $db->permisoEfectivo($permiso));
        }

        $db->enReplica(true);
        $this->assertSame('comun_select', $db->permisoEfectivo('comun'));
        $this->assertSame('sfsv-e_select', $db->permisoEfectivo('sfsv'));
        $this->assertSame('sfsv-e_select', $db->permisoEfectivo('sfsv-e'));
        $this->assertSame('sfsv-e_select', $db->permisoEfectivo('sfsv-d'));
        $this->assertSame('otro', $db->permisoEfectivo('otro'));
    }

    public function test_eliminar_usa_cascade_solo_en_modo_debug(): void
    {
        $log = [];
        ServerConf::$debug = true;
        $this->assertTrue($this->db($this->pdo($log))->borrar('"H-dlb".t'));
        $this->assertSame(['BEGIN', 'DROP TABLE IF EXISTS "H-dlb".t CASCADE;', 'COMMIT'], $log);

        $log = [];
        ServerConf::$debug = false;
        $this->assertTrue($this->db($this->pdo($log))->borrar('"H-dlb".t'));
        $this->assertSame(['BEGIN', 'DROP TABLE IF EXISTS "H-dlb".t RESTRICT;', 'COMMIT'], $log);
    }

    public function test_execute_sql_deshace_todo_y_lanza_excepcion_si_falla_una_sentencia(): void
    {
        $log = [];
        $db = $this->db($this->pdo($log, 2));

        try {
            $db->ejecutar(['SQL 1', 'SQL 2', 'SQL 3']);
            $this->fail('Debía lanzar una excepción');
        } catch (\RuntimeException $e) {
            $this->assertSame('Error ejecutando SQL en base de datos.', $e->getMessage());
        }
        $this->assertSame(['BEGIN', 'SQL 1', 'SQL 2', 'ROLLBACK'], $log);
    }

    public function test_execute_sql_sin_conexion_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->db(null)->ejecutar(['SELECT 1']);
    }

    public function test_table_exists_compara_el_nombre_que_devuelve_to_regclass(): void
    {
        $this->assertFalse($this->db(null)->existe('"H-dlb".t'), 'sin conexión');

        foreach (['"H-dlb".t' => true, '' => false, 'otra' => false] as $devuelto => $esperado) {
            $stmt = $this->createMock(PDOStatement::class);
            $stmt->method('fetch')->willReturn(['to_regclass' => $devuelto === '' ? null : $devuelto]);
            $pdo = $this->createMock(PDO::class);
            $pdo->expects($this->once())->method('query')
                ->with("SELECT to_regclass('\"H-dlb\".t');")
                ->willReturn($stmt);

            $this->assertSame($esperado, $this->db($pdo)->existe('"H-dlb".t'), var_export($devuelto, true));
        }

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn(false);
        $this->assertFalse($this->db($pdo)->existe('"H-dlb".t'), 'la consulta falla');
    }

    public function test_sin_rol_los_permisos_no_hacen_nada(): void
    {
        $log = [];
        $pdo = $this->pdo($log);
        $db = $this->db($pdo);

        foreach (['comun', 'comun_select', 'sfsv', 'sfsv-e', 'sfsv-e_select'] as $base) {
            $db->darPermisoGlobal($base);
            $db->quitarPermisoGlobal($base);
        }
        $db->darPermisoRole('comun', 'H-dlb');
        $db->quitarPermisoRole('sfsv', 'H-dlb');

        $this->assertSame([], $log);
        $this->assertSame($pdo, $db->conexion(), 'no cambia de conexión');
    }

    public function test_dar_o_quitar_a_un_rol_su_propio_rol_no_hace_nada(): void
    {
        $log = [];
        $pdo = $this->pdo($log);

        $db = $this->db($pdo, 'H-dlb', '', '"H-dlb"');
        $db->darPermisoRole('comun', '"H-dlb"');
        $db->quitarPermisoRole('comun', 'H-dlb');

        $db = $this->db($pdo, 'H-dlbv', 'v', '"H-dlb"', '"H-dlbv"');
        $db->darPermisoRole('sfsv', 'H-dlbv');

        $this->assertSame([], $log);
        $this->assertSame($pdo, $db->conexion());
    }

    public function test_create_all_global_sin_replica_se_ejecuta_una_vez(): void
    {
        if (DBAbstract::hasServerSelect()) {
            $this->markTestSkipped('Con réplica de lectura se repite en la réplica y refresca la suscripción (necesita la BD).');
        }
        $db = $this->db(null);
        $llamadas = [];

        $db->crearTodoGlobal(function () use ($db, &$llamadas) {
            $llamadas[] = $db->permisoEfectivo('comun');
        });
        $db->borrarTodoGlobal(function () use ($db, &$llamadas) {
            $llamadas[] = $db->permisoEfectivo('sfsv');
        });

        $this->assertSame(['comun', 'sfsv'], $llamadas);
    }

    public function test_info_table_sin_implementar_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('infoTable no implementado para la tabla: t');
        $this->db(null)->info('t');
    }
}

/** Expone los métodos protegidos de `DBAbstract` con la conexión, el esquema y los roles indicados. */
final class DBAbstractDoble extends DBAbstract
{
    public function __construct(?PDO $pdo, string $esquema, string $vf, string $role, string $roleVf)
    {
        $this->oDbl = $pdo;
        $this->esquema = $esquema;
        $this->vf = $vf;
        $this->role = $role;
        $this->role_vf = $roleVf;
    }

    public function nomTabla(string $tabla): string
    {
        return $this->getNomTabla($tabla);
    }

    public function permisoEfectivo(string $permiso): string
    {
        return $this->permisoGlobalEffective($permiso);
    }

    public function enReplica(bool $valor): void
    {
        $this->operacionEnReplica = $valor;
    }

    public function borrar(string $nomTabla): bool
    {
        return $this->eliminar($nomTabla);
    }

    /** @param list<string> $sql */
    public function ejecutar(array $sql): bool
    {
        return $this->executeSql($sql);
    }

    public function existe(string $nomTabla): bool
    {
        return $this->tableExists($nomTabla);
    }

    public function copiar(string $tabla, string $fichero): void
    {
        $this->copiarDesdeFichero($this->oDbl, $tabla, $fichero, "\t", "\\\\N", 'id');
    }

    public function darPermisoGlobal(string $db): void
    {
        $this->addPermisoGlobal($db);
    }

    public function quitarPermisoGlobal(string $db): void
    {
        $this->delPermisoGlobal($db);
    }

    public function darPermisoRole(string $db, string $role): void
    {
        $this->addPermisoRole($db, $role);
    }

    public function quitarPermisoRole(string $db, string $role): void
    {
        $this->delPermisoRole($db, $role);
    }

    public function conexion(): ?PDO
    {
        return $this->oDbl;
    }

    public function crearTodoGlobal(callable $crear): void
    {
        $this->ejecutarCreateAllGlobal($crear);
    }

    public function borrarTodoGlobal(callable $eliminar): void
    {
        $this->ejecutarDropAllGlobal($eliminar);
    }

    /** @return array<string, mixed> */
    public function info(string $tabla): array
    {
        return $this->infoTable($tabla);
    }
}
