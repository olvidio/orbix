<?php

declare(strict_types=1);

namespace Tests\unit\devel\controller;

use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use PHPUnit\Framework\TestCase;

final class DBRolRenameFlowTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION['oGestorErrores'] = new DummyErrorManager();
    }

    public function test_renombrar_schema_ejecuta_sql_esperado(): void
    {
        $statement = new SpyStatement(true);
        $pdo = new SpyPdo([$statement]);

        $dbRol = new DBRol();
        $dbRol->setDbConexion($pdo);
        $dbRol->setUser('nuevo_esquema');

        $result = $dbRol->renombrarSchema('esquema_viejo');

        $this->assertTrue($result);
        $this->assertSame(
            ['ALTER SCHEMA "esquema_viejo" RENAME TO "nuevo_esquema";'],
            $pdo->preparedSql
        );
        $this->assertSame(1, $statement->executeCalls);
    }

    public function test_renombrar_usuario_ejecuta_rename_password_y_search_path(): void
    {
        $renameStatement = new SpyStatement(true);
        $passwordStatement = new SpyStatement(true);
        $searchPathStatement = new SpyStatement(true);
        $pdo = new SpyPdo([$renameStatement, $passwordStatement, $searchPathStatement]);

        $dbRol = new DBRolSinRepararPostRenombre();
        $dbRol->setDbConexion($pdo);
        $dbRol->setUser('nuevo_usuario');
        $dbRol->setPwd('super_secret');

        $result = $dbRol->renombrarUsuario('usuario_viejo');

        $this->assertTrue($result, 'Tras RENAME + password + search_path, al no existir el esquema en el cluster (prepare del SELECT devuelve false), renombrarUsuario devuelve true sin tocar PostgreSQL.');
        $this->assertSame(
            [
                'ALTER ROLE "usuario_viejo" RENAME TO "nuevo_usuario" ',
                'ALTER USER "nuevo_usuario" WITH PASSWORD \'super_secret\';',
                'ALTER ROLE "nuevo_usuario" SET search_path TO \'nuevo_usuario\', \'public\'; ',
                'SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1',
            ],
            $pdo->preparedSql
        );
        $this->assertSame(1, $renameStatement->executeCalls);
        $this->assertSame(1, $passwordStatement->executeCalls);
        $this->assertSame(1, $searchPathStatement->executeCalls);
    }

    public function test_renombrar_usuario_reporta_error_si_falla_prepare_inicial(): void
    {
        $pdo = new SpyPdo([false]);

        $dbRol = new DBRol();
        $dbRol->setDbConexion($pdo);
        $dbRol->setUser('nuevo_usuario');

        // Al fallar el prepare inicial, DBRol delega en GestorErrores::addErrorAppLastError,
        // cuyo contrato (return type `never`) aborta el flujo lanzando una excepción.
        try {
            $dbRol->renombrarUsuario('usuario_viejo');
            $this->fail('Se esperaba una excepción al fallar el prepare inicial.');
        } catch (\Throwable) {
            // esperado
        }

        $this->assertCount(1, $_SESSION['oGestorErrores']->errors);
        $this->assertSame('DBRol.crear.prepare', $_SESSION['oGestorErrores']->errors[0]['clave']);
    }
}

/**
 * Aísla el flujo unitario de {@see DBRol::renombrarUsuario}: tras los tres prepare/execute del rename,
 * la implementación real comprueba {@see DBRol::existeEsquemaEnCluster} (un prepare SELECT extra) y,
 * sólo si el esquema existe, invoca {@see DBRol::repararEsquemaPostRenombre} (más consultas DDL/DML).
 * Con {@see SpyPdo} el prepare del SELECT devuelve false, así que ese acoplamiento no se ejecuta.
 */
final class DBRolSinRepararPostRenombre extends DBRol
{
    public function repararEsquemaPostRenombre(string $esquemaNombre): bool
    {
        return true;
    }
}

final class SpyPdo extends \PDO
{
    public array $preparedSql = [];

    /** @var array<int, SpyStatement|false> */
    private array $prepareResults;

    /** @param array<int, SpyStatement|false> $prepareResults */
    public function __construct(array $prepareResults)
    {
        // No se invoca parent::__construct() a propósito: no queremos abrir
        // ninguna conexión real; sólo necesitamos cumplir el type-hint PDO.
        $this->prepareResults = $prepareResults;
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false
    {
        $this->preparedSql[] = $query;
        return array_shift($this->prepareResults) ?? false;
    }
}

final class SpyStatement extends \PDOStatement
{
    public int $executeCalls = 0;

    public function __construct(private readonly bool $executeResult)
    {
    }

    public function execute(?array $params = null): bool
    {
        $this->executeCalls++;
        return $this->executeResult;
    }
}

/**
 * Doble de {@see GestorErrores} que captura los errores en memoria. Debe extender
 * la clase real porque {@see DBRol} sólo reconoce el gestor si es `instanceof GestorErrores`.
 * No se invoca el constructor padre para no depender de ConfigGlobal ni del fichero de log.
 */
final class DummyErrorManager extends GestorErrores
{
    /** @var array<int, array{clave: string, line: string, file: string}> */
    public array $errors = [];

    public function __construct()
    {
    }

    public function addErrorAppLastError(\PDOStatement|\PDO $oDBSt, string $sClauError, string $line, string $file): never
    {
        $this->errors[] = [
            'clave' => $sClauError,
            'line' => $line,
            'file' => $file,
        ];

        // Replica el contrato `never` de GestorErrores: tras registrar, aborta el flujo.
        throw new \RuntimeException($sClauError);
    }

    public function addErrorAppLastErrorNoThrowText(string $errorText, string $sClauError, string $line, string $file): void
    {
        $this->errors[] = [
            'clave' => $sClauError,
            'line' => $line,
            'file' => $file,
        ];
    }
}
