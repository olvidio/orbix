<?php

declare(strict_types=1);

namespace Tests\unit\devel\application;

use PHPUnit\Framework\TestCase;
use src\devel_db_admin\application\RenombrarEsquema;
use src\shared\infrastructure\persistence\postgresql\DBRol;

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
        $instance = new RenombrarEsquema(new \stdClass());
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

    /** @return \Closure(SpyDBRol, SpyPdoIdem, string, string): void */
    private function makeRenombrarSoloEsquemaHelper(): \Closure
    {
        $instance = new RenombrarEsquema(new \stdClass());
        $method = new \ReflectionMethod($instance, 'renombrarBloqueSoloEsquema');

        return static function (
            SpyDBRol $oDBRol,
            SpyPdoIdem $pdo,
            string $old,
            string $new
        ) use ($instance, $method): void {
            $method->invoke($instance, $oDBRol, $pdo, $old, $new);
        };
    }

    public function test_estado_inicial_renombra_schema_y_rol(): void
    {
        $pdo = new SpyPdoIdem(['esquema_old'], ['esquema_old']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', 'pwd');

        $this->assertSame(['esquema_old'], $dbRol->renombrarSchemaCalls);
        $this->assertSame(['esquema_old'], $dbRol->renombrarUsuarioCalls);
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

    public function test_si_rol_ya_renombrado_solo_renombra_schema_y_repara(): void
    {
        $pdo = new SpyPdoIdem(['esquema_old', 'esquema_new'], ['esquema_new']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarHelper();

        $invoke($dbRol, $pdo, 'esquema_old', 'esquema_new', 'pwd');

        $this->assertSame(
            [],
            $dbRol->renombrarSchemaCalls,
            'No debe renombrar el esquema si el nuevo ya existe (coexistencia es incoherencia, pero no la creamos nosotros).',
        );
        $this->assertSame([], $dbRol->renombrarUsuarioCalls);
        $this->assertSame(['esquema_new'], $dbRol->repararEsquemaPostRenombreCalls);
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

        $invoke($dbRol, $pdo, 'esquema_oldv', 'esquema_newv');

        $this->assertSame(['esquema_oldv'], $dbRol->renombrarSchemaCalls);
        $this->assertSame([], $dbRol->renombrarUsuarioCalls, 'sv-e no debe tocar el rol (ya lo hizo el bloque sv).');
        $this->assertSame(
            [],
            $dbRol->repararEsquemaPostRenombreCalls,
            'En esta primera pasada el esquema nuevo todavía no existe en pdo, así que no podemos reparar.',
        );
    }

    public function test_sve_segunda_pasada_no_renombra_pero_repara(): void
    {
        $pdo = new SpyPdoIdem(['esquema_newv'], ['esquema_newv']);
        $dbRol = new SpyDBRol();
        $invoke = $this->makeRenombrarSoloEsquemaHelper();

        $invoke($dbRol, $pdo, 'esquema_oldv', 'esquema_newv');

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

    /**
     * @param mixed $esquema_old
     * @return null
     */
    public function renombrarSchema($esquema_old)
    {
        $this->renombrarSchemaCalls[] = is_string($esquema_old) ? $esquema_old : '';

        return null;
    }

    /**
     * @param mixed $usuario_old
     * @return null
     */
    public function renombrarUsuario($usuario_old)
    {
        $this->renombrarUsuarioCalls[] = is_string($usuario_old) ? $usuario_old : '';

        return null;
    }

    public function repararEsquemaPostRenombre(string $esquemaNombre): bool
    {
        $this->repararEsquemaPostRenombreCalls[] = $esquemaNombre;

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
