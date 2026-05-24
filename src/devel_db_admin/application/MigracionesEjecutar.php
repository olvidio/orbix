<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use RuntimeException;
use Throwable;
use src\devel_db_admin\application\services\MigracionCsvPuente;
use src\devel_db_admin\application\services\MigracionSqlAnalyzer;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

final class MigracionesEjecutar
{
    /**
     * @var array<string, list<string>>
     */
    private array $schemaCache = [];

    /**
     * @var array<int, true>
     */
    private array $bootstrapPorPdo = [];

    public function __construct(
        private readonly object $container,
        private readonly ?string $migrationsDir = null,
        private readonly ?MigracionSqlAnalyzer $analyzer = null,
    ) {
    }

    /**
     * @param list<string> $seleccionados
     * @return array{lines: list<string>, error: string|null}
     */
    public function ejecutar(string $modo, array $seleccionados, string $prefijoHasta = ''): array
    {
        $repo = $this->container->get(MigracionAplicadaRepositoryInterface::class);
        $repo->ensureTabla();

        $scan = (new MigracionesEscanear($repo, $this->migrationsDir, $this->analyzer))->escanear();
        $migraciones = $this->migracionesPorId($scan['migraciones']);
        $aEjecutar = $this->resolverMigraciones($modo, $seleccionados, $prefijoHasta, $migraciones);

        if ($aEjecutar === []) {
            return [
                'lines' => ['No hay migraciones para ejecutar.'],
                'error' => null,
            ];
        }

        $lines = [];
        $analyzer = $this->analyzer ?? new MigracionSqlAnalyzer();
        foreach ($aEjecutar as $migracion) {
            $lines[] = sprintf('Migracion %s_%s', $migracion['prefijo'], $migracion['descripcion']);
            foreach ((array) $migracion['aplicaciones'] as $aplicacion) {
                $result = $this->ejecutarAplicacion(
                    $repo,
                    $analyzer,
                    $aplicacion,
                    $modo === 'seleccion',
                );
                $lines = array_merge($lines, $result['lines']);
                if ($result['error'] !== null) {
                    return [
                        'lines' => $lines,
                        'error' => $result['error'],
                    ];
                }
            }
        }

        $lines[] = 'Migraciones finalizadas.';

        return [
            'lines' => $lines,
            'error' => null,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $migraciones
     * @return array<string, array<string, mixed>>
     */
    private function migracionesPorId(array $migraciones): array
    {
        $index = [];
        foreach ($migraciones as $migracion) {
            $index[(string) $migracion['id']] = $migracion;
        }
        ksort($index, SORT_STRING);

        return $index;
    }

    /**
     * @param list<string> $seleccionados
     * @param array<string, array<string, mixed>> $migraciones
     * @return array<int, array<string, mixed>>
     */
    private function resolverMigraciones(string $modo, array $seleccionados, string $prefijoHasta, array $migraciones): array
    {
        if ($modo === 'hasta') {
            if ($prefijoHasta === '' || !isset($migraciones[$prefijoHasta])) {
                return [];
            }

            $result = [];
            foreach ($migraciones as $id => $migracion) {
                if ($id > $prefijoHasta) {
                    break;
                }
                $result[] = $migracion;
            }

            return $result;
        }

        $selected = array_fill_keys($seleccionados, true);
        $result = [];
        foreach ($migraciones as $id => $migracion) {
            if (isset($selected[$id])) {
                $result[] = $migracion;
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $aplicacion
     * @return array{lines: list<string>, error: string|null}
     */
    private function ejecutarAplicacion(
        MigracionAplicadaRepositoryInterface $repo,
        MigracionSqlAnalyzer $analyzer,
        array $aplicacion,
        bool $reaplicarSeleccion = false,
    ): array {
        $prefijo = (string) $aplicacion['prefijo'];
        $descripcion = (string) $aplicacion['descripcion'];
        $database = (string) $aplicacion['database'];
        $sha1 = (string) $aplicacion['sha1'];
        $tipo = (string) $aplicacion['tipo'];
        $file = (string) $aplicacion['file'];
        $path = (string) $aplicacion['path'];

        $aplicada = $repo->findByKey($prefijo, $descripcion, $database);
        $lines = [];
        if ($aplicada instanceof MigracionAplicada && $aplicada->isOk() && !$reaplicarSeleccion) {
            if ($aplicada->getSha1() === $sha1) {
                return [
                    'lines' => [sprintf('  - %s en %s: ya aplicada.', $file, $database)],
                    'error' => null,
                ];
            }

            $lines[] = sprintf(
                '  - %s en %s: contenido cambiado; reaplicando (idempotente)...',
                $file,
                $database,
            );
        } elseif ($aplicada instanceof MigracionAplicada && $aplicada->isOk() && $reaplicarSeleccion) {
            $lines[] = sprintf(
                '  - %s en %s: reaplicando por seleccion explicita (idempotente)...',
                $file,
                $database,
            );
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            $error = sprintf('No se puede leer %s', $file);
            $this->registrar($repo, $aplicacion, false, $error);

            return [
                'lines' => ['  - ' . $error],
                'error' => $error,
            ];
        }

        $usaComodin = $analyzer->usaComodin($sql);
        if ($lines === []) {
            $lines = [sprintf('  - Ejecutando %s en %s%s', $file, $database, $usaComodin ? ' (por esquemas)' : '')];
        }

        try {
            $pdo = $this->connect($database);
            $this->ensureMigracionBootstrap($pdo);
            $puente = new MigracionCsvPuente();
            $schemas = $usaComodin ? $this->schemasParaDatabase($database) : [];
            if ($usaComodin && $schemas === []) {
                throw new RuntimeException(sprintf('No se han encontrado esquemas activos para %s', $database));
            }
            $lines = array_merge($lines, $this->executeSql($pdo, $sql, $schemas, $analyzer, $puente, $database));
            $this->registrar($repo, $aplicacion, true, null);
            if (!$usaComodin) {
                $lines[] = '    ok';
            }

            return [
                'lines' => $lines,
                'error' => null,
            ];
        } catch (Throwable $e) {
            if ($this->esMigracionYaAplicada($e)) {
                $this->registrar($repo, $aplicacion, true, 'ya estaba corregido');
                $lines[] = '    ok (ya estaba corregido)';

                return [
                    'lines' => $lines,
                    'error' => null,
                ];
            }

            $error = $e->getMessage();
            $this->registrar($repo, $aplicacion, false, $error);
            $lines[] = '    ERROR: ' . $error;

            return [
                'lines' => $lines,
                'error' => $error,
            ];
        }
    }

    private function esMigracionYaAplicada(Throwable $e): bool
    {
        if ($e instanceof PDOException && (string) ($e->errorInfo[0] ?? '') === 'P0002') {
            return true;
        }

        $msg = $e->getMessage();

        return str_contains($msg, 'MIGRACION_YA_APLICADA')
            || str_contains($msg, 'SQLSTATE[P0002]');
    }

    private function connect(string $database): PDO
    {
        $schema = match ($database) {
            MigracionDatabase::COMUN => 'public',
            MigracionDatabase::COMUN_SELECT => 'public_select',
            MigracionDatabase::SV => 'publicv',
            MigracionDatabase::SV_E => 'publicv-e',
            MigracionDatabase::SV_E_SELECT => 'publicv-e_select',
            default => throw new RuntimeException(sprintf('Database no soportada: %s', $database)),
        };

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema($schema);
        $oConexion = new DBConnection($config);

        $pdo = $oConexion->getPDO();
        // ConfigDB usa labels tipo publicv-e_select como search_path; ese namespace no existe
        // en PostgreSQL. Las migraciones usan nombres cualificados (global.*, publicv.*, etc.).
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    /**
     * @param list<string> $schemas
     * @return list<string>
     */
    private function executeSql(
        PDO $pdo,
        string $sql,
        array $schemas,
        MigracionSqlAnalyzer $analyzer,
        MigracionCsvPuente $puente,
        string $database,
    ): array {
        $plan = $puente->parse($sql);
        $log = [];
        $esPrimaria = !MigracionEjecucionUtiles::esReplicaSelect($database);

        if ($puente->tieneExport($plan)) {
            if ($esPrimaria) {
                $log = array_merge($log, $puente->export($pdo, $plan));
            } else {
                $log[] = '    export CSV omitido en replica de lectura (solo BD primaria)';
            }
        }

        if ($schemas === []) {
            $pdo->beginTransaction();
            try {
                if ($plan['sql_before_import'] !== '') {
                    $this->execOrFail($pdo, $plan['sql_before_import']);
                }
                if ($puente->tieneImport($plan)) {
                    if ($esPrimaria) {
                        $log = array_merge($log, $puente->import($pdo, $plan));
                    } else {
                        $log[] = '    import CSV omitido en replica de lectura (solo BD primaria)';
                    }
                }
                if ($plan['sql_after_import'] !== '') {
                    $this->execOrFail($pdo, $plan['sql_after_import']);
                }
                $pdo->commit();
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $e;
            }

            return $log;
        }

        $sqlComodin = trim($plan['sql_before_import'] . "\n" . $plan['sql_after_import']);
        if ($sqlComodin === '') {
            return $log;
        }

        $ok = 0;
        $omitidosEsquema = 0;
        foreach ($schemas as $schema) {
            if (!MigracionEjecucionUtiles::esquemaExisteEnPostgres($pdo, $schema)) {
                $omitidosEsquema++;
                $log[] = sprintf(
                    '    omitido "%s" (el esquema no existe en esta base de datos; no se ejecuta el SQL)',
                    $schema,
                );
                continue;
            }
            $expandido = $analyzer->expandirComodin($sqlComodin, $schema);
            $pdo->beginTransaction();
            try {
                $this->execOrFail($pdo, $expandido);
                $pdo->commit();
                $ok++;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if (MigracionEjecucionUtiles::esOmitiblePorAusenciaDeEsquema($e, $pdo, $schema)) {
                    $omitidosEsquema++;
                    $log[] = sprintf(
                        '    omitido "%s" (esquema inexistente en esta BD): %s',
                        $schema,
                        $e->getMessage(),
                    );
                    continue;
                }
                throw $e;
            }
        }
        $log[] = sprintf('    aplicado en %d esquema(s)', $ok);
        if ($omitidosEsquema > 0) {
            $log[] = sprintf('    omitidos %d esquema(s) inexistentes', $omitidosEsquema);
        }
        if ($ok === 0) {
            throw new RuntimeException(
                'La migracion no se aplico en ningun esquema: todos omitidos por esquema inexistente (catalogo PostgreSQL / SQLSTATE 3F000 / 42P01).',
            );
        }

        return $log;
    }

    private function ensureMigracionBootstrap(PDO $pdo): void
    {
        $id = spl_object_id($pdo);
        if (isset($this->bootstrapPorPdo[$id])) {
            return;
        }

        $dir = $this->migrationsDir ?? dirname(__DIR__, 3) . '/db/migrations';
        $path = $dir . '/_bootstrap/migracion_idempotente.sql';
        if (is_file($path)) {
            $bootstrap = file_get_contents($path);
            if ($bootstrap !== false && MigracionEjecucionUtiles::tieneSqlEjecutable($bootstrap)) {
                $this->execSqlScript($pdo, $bootstrap);
            }
        }

        $this->bootstrapPorPdo[$id] = true;
    }

    private function execOrFail(PDO $pdo, string $sql): void
    {
        $this->execSqlScript($pdo, $sql);
    }

    private function execSqlScript(PDO $pdo, string $sql): void
    {
        foreach (MigracionEjecucionUtiles::splitSqlStatements($sql) as $statement) {
            if (!MigracionEjecucionUtiles::tieneSqlEjecutable($statement)) {
                continue;
            }

            if (preg_match('/^SELECT\s+/i', ltrim($statement)) === 1) {
                $stmt = $pdo->query($statement);
                if ($stmt !== false) {
                    $stmt->closeCursor();
                }
                continue;
            }

            $result = $pdo->exec($statement);
            if ($result === false) {
                $this->lanzarExcepcionSql($pdo);
            }
        }
    }

    private function lanzarExcepcionSql(PDO $pdo): void
    {
        $info = $pdo->errorInfo();
        $sqlState = (string) ($info[0] ?? 'HY000');
        $message = (string) ($info[2] ?? 'sin detalle');

        if ($sqlState === 'P0002' || str_contains($message, 'MIGRACION_YA_APLICADA')) {
            throw new RuntimeException(sprintf('SQLSTATE[%s]: %s', $sqlState, $message));
        }

        throw new RuntimeException(sprintf(
            'Error ejecutando SQL de migracion (%s): %s',
            $sqlState,
            $message,
        ));
    }

    /**
     * @return list<string>
     */
    private function schemasParaDatabase(string $database): array
    {
        $tipo = in_array($database, [MigracionDatabase::COMUN, MigracionDatabase::COMUN_SELECT], true)
            ? 'comun'
            : 'sv';

        if (isset($this->schemaCache[$tipo])) {
            return $this->schemaCache[$tipo];
        }

        $repository = $this->container->get(DbSchemaRepositoryInterface::class);
        $schemas = [];
        foreach ($repository->getDbSchemas(['_ordre' => 'schema']) as $dbSchema) {
            $schema = $dbSchema->getSchema();
            if (MigracionEjecucionUtiles::esEsquemaResto($schema)) {
                continue;
            }
            if ($tipo === 'comun' && MigracionEjecucionUtiles::esEsquemaRegionStgrComun($schema)) {
                continue;
            }
            if ($tipo === 'comun') {
                if ($dbSchema->getId() >= 3000 && $dbSchema->getId() < 4000 && !str_ends_with($schema, 'v') && !str_ends_with($schema, 'f')) {
                    $schemas[] = $schema;
                }
                continue;
            }

            if (str_ends_with($schema, 'v')) {
                $schemas[] = $schema;
            }
        }
        sort($schemas, SORT_STRING);
        $this->schemaCache[$tipo] = array_values(array_unique($schemas));

        return $this->schemaCache[$tipo];
    }

    /**
     * @param array<string, mixed> $aplicacion
     */
    private function registrar(
        MigracionAplicadaRepositoryInterface $repo,
        array $aplicacion,
        bool $ok,
        ?string $mensaje,
    ): void {
        $migracion = new MigracionAplicada();
        $migracion->setPrefijo((string) $aplicacion['prefijo']);
        $migracion->setDescripcion((string) $aplicacion['descripcion']);
        $migracion->setDatabase((string) $aplicacion['database']);
        $migracion->setTipo((string) $aplicacion['tipo']);
        $migracion->setSha1((string) $aplicacion['sha1']);
        $migracion->setUsuario($this->usuarioActual());
        $migracion->setOk($ok);
        $migracion->setMensaje($mensaje);
        $repo->registrar($migracion);
    }

    private function usuarioActual(): ?string
    {
        try {
            return (string) ConfigGlobal::mi_usuario();
        } catch (Throwable) {
            return null;
        }
    }
}
