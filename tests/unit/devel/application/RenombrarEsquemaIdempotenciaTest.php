<?php

declare(strict_types=1);

namespace Tests\unit\devel\application;

use PHPUnit\Framework\TestCase;
use src\devel_db_admin\application\RenombrarEsquema;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Cubre el escenario «rename a medias»: si una pasada anterior renombró ya algún esquema/rol/clave
 * (por ejemplo, comun OK pero falló en public_select), un segundo intento debe completar lo que falte
 * y no reintentar lo ya hecho.
 *
 * No requiere PostgreSQL: se inyectan dobles para `PDO` (SpyPdoIdem) y `DBRol` (SpyDBRol) y se invocan
 * los helpers privados de {@see RenombrarEsquema} con reflexión.
 */
final class RenombrarEsquemaIdempotenciaTest extends TestCase
{
    /** @return \Closure(SpyDBRol, SpyPdoIdem, string, string, ?string): void */
    private function makeRenombrarHelper(): \Closure
    {
        $instance = new RenombrarEsquema($this->createStub(DbSchemaRepositoryInterface::class));
        $method = new \ReflectionMethod($instance, 'renombrarBloqueRolEsquema');

        return static function (
            SpyDBRol $oDBRol,
            SpyPdoIdem $pdo,
            string $old,
            string $new,
            ?string $pwd
        ) use ($instance, $method): void {
            $method->invoke($instance, $oDBRol, $pdo, $old, $new, $pwd);
        };
    }

    /** @return \Closure(SpyDBRol, SpyPdoIdem, string, string, ?string): void */
    private function makeRenombrarSoloEsquemaHelper(): \Closure
    {
        $instance = new RenombrarEsquema($this->createStub(DbSchemaRepositoryInterface::class));
        $method = new \ReflectionMethod($instance, 'renombrarBloqueSoloEsquema');

        return static function (
            SpyDBRol $oDBRol,
            SpyPdoIdem $pdo,
            string $old,
            string $new,
            ?string $pwd = null,
        ) use ($instance, $method): void {
            $method->invoke($instance, $oDBRol, $pdo, $old, $new, $pwd);
        };
    }

    public function test_estado_inicial_renombra_schema_y_rol(): void
    {
        $pdo = new SpyPdoIdem(['esquema_old'], ['esquema_old']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', 'pwd');

        $this->assertSame(['esquema_old'], $dbRol->renombrarUsuarioCalls, 'El rol debe renombrarse antes que el esquema.');
        $this->assertSame(['esquema_old'], $dbRol->renombrarSchemaCalls);
        $this->assertSame([], $dbRol->repararEsquemaPostRenombreCalls);
        $this->assertSame('esquema_new', $dbRol->lastUser);
        $this->assertSame('pwd', $dbRol->lastPwd);
    }

    public function test_si_schema_ya_renombrado_solo_renombra_rol(): void
    {
        $pdo = new SpyPdoIdem(['esquema_new'], ['esquema_old']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', 'pwd');

        $this->assertSame([], $dbRol->renombrarSchemaCalls, 'No debe rehacer ALTER SCHEMA si el nuevo ya existe.');
        $this->assertSame(['esquema_old'], $dbRol->renombrarUsuarioCalls);
        $this->assertSame([], $dbRol->repararEsquemaPostRenombreCalls);
    }

    public function test_si_rol_ya_renombrado_solo_repara_si_esquema_destino_ya_existe(): void
    {
        $pdo = new SpyPdoIdem(['esquema_old', 'esquema_new'], ['esquema_new']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', 'pwd');

        $this->assertSame([], $dbRol->renombrarSchemaCalls);
        $this->assertSame([], $dbRol->renombrarUsuarioCalls);
        $this->assertSame(['esquema_new'], $dbRol->repararEsquemaPostRenombreCalls);
    }

    public function test_si_esquema_ya_renombrado_y_rol_ausente_crea_rol_con_password(): void
    {
        $pdo = new SpyPdoIdem(['esquema_new'], []);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'B-crBv', 'V-crVv', 'pwd-sv');

        $this->assertSame([], $dbRol->renombrarSchemaCalls);
        $this->assertSame([], $dbRol->renombrarUsuarioCalls);
        $this->assertSame(['V-crVv'], $dbRol->crearUsuarioCalls);
        $this->assertSame([], $dbRol->repararEsquemaPostRenombreCalls);
    }

    public function test_si_todo_ya_renombrado_solo_repara(): void
    {
        $pdo = new SpyPdoIdem(['esquema_new'], ['esquema_new']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', 'pwd');

        $this->assertSame([], $dbRol->renombrarSchemaCalls);
        $this->assertSame([], $dbRol->renombrarUsuarioCalls);
        $this->assertSame(
            ['esquema_new'],
            $dbRol->repararEsquemaPostRenombreCalls,
            'Aseguramos propietarios/privilegios aunque el rename ya estuviera hecho (idempotente).',
        );
    }

    public function test_password_opcional_no_se_propaga_si_null(): void
    {
        $pdo = new SpyPdoIdem(['esquema_old'], ['esquema_old']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', null);

        $this->assertNull($dbRol->lastPwd, 'Si no hay password en ningún .inc no debemos pisar el que pueda haber configurado el caller.');
    }

    public function test_sve_renombra_solo_schema_y_repara_idempotente(): void
    {
        $pdo = new SpyPdoIdem(['esquema_oldv'], ['esquema_oldv']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarSoloEsquemaHelper();

        $invoke($dbRol, $pdo, 'esquema_oldv', 'esquema_newv', 'pwd');

        $this->assertSame(['esquema_oldv'], $dbRol->renombrarUsuarioCalls);
        $this->assertSame(['esquema_oldv'], $dbRol->renombrarSchemaCalls);
        $this->assertSame(
            [],
            $dbRol->repararEsquemaPostRenombreCalls,
            'Tras renombrar el esquema el spy no refleja el rol nuevo; reparar queda para la segunda pasada.',
        );
    }

    public function test_sve_segunda_pasada_no_renombra_pero_repara(): void
    {
        $pdo = new SpyPdoIdem(['esquema_newv'], ['esquema_newv']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarSoloEsquemaHelper();

        $invoke($dbRol, $pdo, 'esquema_oldv', 'esquema_newv', 'pwd');

        $this->assertSame([], $dbRol->renombrarSchemaCalls);
        $this->assertSame(['esquema_newv'], $dbRol->repararEsquemaPostRenombreCalls);
    }
}

/** Cuenta llamadas relevantes; reemplaza a {@see DBRol} en los tests. */
final class SpyDBRol extends DBRol
{
    /** @var list<string> */
    public array $renombrarSchemaCalls = [];
    /** @var list<string> */
    public array $renombrarUsuarioCalls = [];
    /** @var list<string> */
    public array $repararEsquemaPostRenombreCalls = [];
    /** @var list<string> */
    public array $crearUsuarioCalls = [];

    public ?string $lastUser = null;
    public ?string $lastPwd = null;

    /** @param mixed $oDbl */
    public function setDbConexion($oDbl): void
    {
        // No queremos conexión real; ignorar.
    }

    /** @param mixed $user */
    public function setUser($user): void
    {
        $this->lastUser = is_string($user) ? $user : null;
    }

    /** @param mixed $password */
    public function setPwd($password): void
    {
        $this->lastPwd = is_string($password) ? $password : null;
    }

    public function renombrarSchema(string $esquema_old): bool
    {
        $this->renombrarSchemaCalls[] = $esquema_old;

        return true;
    }

    public function renombrarUsuario(string $usuario_old): bool
    {
        $this->renombrarUsuarioCalls[] = $usuario_old;

        return true;
    }

    public function repararEsquemaPostRenombre(string $esquemaNombre): bool
    {
        $this->repararEsquemaPostRenombreCalls[] = $esquemaNombre;

        return true;
    }

    public function crearUsuario(): bool
    {
        $this->crearUsuarioCalls[] = (string) $this->lastUser;

        return true;
    }
}

/**
 * Mini PDO que responde a las consultas `SELECT 1 FROM pg_namespace…` / `SELECT 1 FROM pg_roles…`
 * usadas por {@see RenombrarEsquema::existeEsquema()} y `existeRol()`.
 */
final class SpyPdoIdem extends \PDO
{
    /** @var list<string> */
    private readonly array $esquemas;
    /** @var list<string> */
    private readonly array $roles;

    /**
     * @param list<string> $esquemas
     * @param list<string> $roles
     */
    public function __construct(array $esquemas, array $roles)
    {
        // No llamamos a parent::__construct: no queremos abrir conexión real.
        $this->esquemas = $esquemas;
        $this->roles = $roles;
    }

    /**
     * @param string $query
     * @param array<int|string, mixed> $options
     */
    public function prepare($query, $options = []): \PDOStatement
    {
        if (str_contains($query, 'pg_namespace')) {
            return new SpyPdoIdemStatement($this->esquemas);
        }
        if (str_contains($query, 'pg_roles')) {
            return new SpyPdoIdemStatement($this->roles);
        }

        return new SpyPdoIdemStatement([]);
    }
}

final class SpyPdoIdemStatement extends \PDOStatement
{
    private bool $hit = false;
    /** @var list<string> */
    private readonly array $existing;

    /** @param list<string> $existing */
    public function __construct(array $existing)
    {
        $this->existing = $existing;
    }

    /** @param array<int|string, mixed>|null $params */
    public function execute($params = null): bool
    {
        $needle = '';
        if (is_array($params)) {
            $val = reset($params);
            $needle = is_string($val) ? $val : '';
        }
        $this->hit = $needle !== '' && in_array($needle, $this->existing, true);

        return true;
    }

    public function fetchColumn($column = 0): mixed
    {
        return $this->hit ? 1 : false;
    }
}
