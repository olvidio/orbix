<?php

declare(strict_types=1);

namespace Tests\unit\devel\controller;

use core\DBRol;
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

        $this->assertNull($result);
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

        $dbRol = new DBRol();
        $dbRol->setDbConexion($pdo);
        $dbRol->setUser('nuevo_usuario');
        $dbRol->setPwd('super_secret');

        $result = $dbRol->renombrarUsuario('usuario_viejo');

        $this->assertNull($result);
        $this->assertSame(
            [
                'ALTER ROLE "usuario_viejo" RENAME TO "nuevo_usuario" ',
                'ALTER USER "nuevo_usuario" WITH PASSWORD \'super_secret\';',
                'ALTER ROLE "nuevo_usuario" SET search_path TO \'nuevo_usuario\', \'public\'; ',
            ],
            $pdo->preparedSql
        );
        $this->assertSame(1, $renameStatement->executeCalls);
        $this->assertSame(1, $passwordStatement->executeCalls);
        $this->assertSame(1, $searchPathStatement->executeCalls);
    }

    public function test_renombrar_usuario_devuelve_false_si_falla_prepare_inicial(): void
    {
        $pdo = new SpyPdo([false]);

        $dbRol = new DBRol();
        $dbRol->setDbConexion($pdo);
        $dbRol->setUser('nuevo_usuario');

        $result = $dbRol->renombrarUsuario('usuario_viejo');

        $this->assertFalse($result);
        $this->assertCount(1, $_SESSION['oGestorErrores']->errors);
        $this->assertSame('DBRol.crear.prepare', $_SESSION['oGestorErrores']->errors[0]['clave']);
    }
}

final class SpyPdo
{
    public array $preparedSql = [];

    /** @var array<int, SpyStatement|false> */
    private array $prepareResults;

    /** @param array<int, SpyStatement|false> $prepareResults */
    public function __construct(array $prepareResults)
    {
        $this->prepareResults = $prepareResults;
    }

    public function prepare(string $sql)
    {
        $this->preparedSql[] = $sql;
        return array_shift($this->prepareResults) ?? false;
    }
}

final class SpyStatement
{
    public int $executeCalls = 0;

    public function __construct(private readonly bool $executeResult)
    {
    }

    public function execute(): bool
    {
        $this->executeCalls++;
        return $this->executeResult;
    }
}

final class DummyErrorManager
{
    /** @var array<int, array{clave: string, line: int, file: string}> */
    public array $errors = [];

    public function addErrorAppLastError($obj, string $clave, int $line, string $file): void
    {
        $this->errors[] = [
            'clave' => $clave,
            'line' => $line,
            'file' => $file,
        ];
    }
}
