<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;
use src\devel_db_admin\application\services\MigracionCsvPuente;
use src\devel_db_admin\application\services\MigracionSqlAnalyzer;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use src\devel_db_admin\domain\value_objects\MigracionTipo;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
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
        private readonly MigracionAplicadaRepositoryInterface $migracionRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
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
        $repo = $this->migracionRepository;
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

        $serie = is_scalar($scan['serie'] ?? null) ? (string) $scan['serie'] : MigracionesEscanear::serieDesdeSesion();
        if ($serie === MigracionDatabase::SERIE_SF) {
            $lines = [sprintf('Serie migraciones: %s (BD sf, sin réplica).', $serie)];
        } else {
            $lines = [];
        }
        $analyzer = $this->analyzer ?? new MigracionSqlAnalyzer();
        foreach ($aEjecutar as $migracion) {
            $migracionAplicaciones = self::normalizeRows($migracion['aplicaciones'] ?? []);
            $lines[] = sprintf(
                'Migracion %s_%s',
                $this->toScalarString($migracion['prefijo'] ?? null),
                $this->toScalarString($migracion['descripcion'] ?? null),
            );
            $migracion['aplicaciones'] = $migracionAplicaciones;
            $lines = array_merge($lines, $this->suspenderSuscripcionesReplicacion($migracion));
            $errorMigracion = null;
            foreach ($migracionAplicaciones as $aplicacion) {
                $result = $this->ejecutarAplicacion(
                    $repo,
                    $analyzer,
                    $aplicacion,
                    $modo === 'seleccion',
                );
                $lines = array_merge($lines, $result['lines']);
                if ($result['error'] !== null) {
                    $errorMigracion = $result['error'];
                    break;
                }
            }
            if ($errorMigracion === null) {
                $lines = array_merge($lines, $this->reactivarSuscripcionesReplicacion($migracion));
            } else {
                $lines = array_merge(
                    $lines,
                    $this->avisosSuscripcionesSinReactivar($migracion),
                );
            }

            if ($errorMigracion !== null) {
                return [
                    'lines' => $lines,
                    'error' => $errorMigracion,
                ];
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
            $idVal = $migracion['id'] ?? '';
            $id = is_scalar($idVal) ? (string) $idVal : '';
            if ($id === '') {
                continue;
            }
            $index[$id] = $migracion;
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
        $prefijo = is_scalar($aplicacion['prefijo'] ?? null) ? (string) $aplicacion['prefijo'] : '';
        $descripcion = is_scalar($aplicacion['descripcion'] ?? null) ? (string) $aplicacion['descripcion'] : '';
        $database = is_scalar($aplicacion['database'] ?? null) ? (string) $aplicacion['database'] : '';
        $sha1 = is_scalar($aplicacion['sha1'] ?? null) ? (string) $aplicacion['sha1'] : '';
        $tipo = is_scalar($aplicacion['tipo'] ?? null) ? (string) $aplicacion['tipo'] : '';
        $file = is_scalar($aplicacion['file'] ?? null) ? (string) $aplicacion['file'] : '';
        $path = is_scalar($aplicacion['path'] ?? null) ? (string) $aplicacion['path'] : '';

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
        $config = match ($database) {
            MigracionDatabase::COMUN => $this->configImportar('public'),
            MigracionDatabase::COMUN_SELECT => $this->configImportar('public_select'),
            MigracionDatabase::SV => $this->configImportar('publicv'),
            MigracionDatabase::SV_E => $this->configImportar('publicv-e'),
            MigracionDatabase::SV_E_SELECT => $this->configImportar('publicv-e_select'),
            // Serie sf: conexión normal a BD sf (no depende del bloque default de importar).
            MigracionDatabase::SF => $this->configSf(),
            default => throw new RuntimeException(sprintf('Database no soportada: %s', $database)),
        };

        $oConexion = new DBConnection($config);

        $pdo = $oConexion->getPDO();
        // ConfigDB usa labels tipo publicv-e_select como search_path; ese namespace no existe
        // en PostgreSQL. Las migraciones usan nombres cualificados (global.*, publicv.*, etc.).
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    /**
     * @return array<string, mixed>
     */
    private function configImportar(string $claveEsquema): array
    {
        $oConfigDB = new ConfigDB('importar');

        return $oConfigDB->getEsquema($claveEsquema);
    }

    /**
     * BD sf: preferir ConfigDB('sf')/publicf; si falta, importar/publicf (mantenimiento).
     *
     * @return array<string, mixed>
     */
    private function configSf(): array
    {
        try {
            return (new ConfigDB('sf'))->getEsquema('publicf');
        } catch (Throwable $eSf) {
            try {
                return (new ConfigDB('importar'))->getConexionMantenimiento('publicf');
            } catch (Throwable $eImportar) {
                throw new RuntimeException(sprintf(
                    'No se puede conectar a sf: %s (fallback importar: %s)',
                    $eSf->getMessage(),
                    $eImportar->getMessage(),
                ), 0, $eSf);
            }
        }
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
        $tipo = match (true) {
            in_array($database, [MigracionDatabase::COMUN, MigracionDatabase::COMUN_SELECT], true) => 'comun',
            $database === MigracionDatabase::SF => 'sf',
            default => 'sv',
        };

        if (isset($this->schemaCache[$tipo])) {
            return $this->schemaCache[$tipo];
        }

        $repository = $this->dbSchemaRepository;
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

            if ($tipo === 'sf') {
                if (str_ends_with($schema, 'f')) {
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
        $migracion->setPrefijo(is_scalar($aplicacion['prefijo'] ?? null) ? (string) $aplicacion['prefijo'] : '');
        $migracion->setDescripcion(is_scalar($aplicacion['descripcion'] ?? null) ? (string) $aplicacion['descripcion'] : '');
        $migracion->setDatabase(is_scalar($aplicacion['database'] ?? null) ? (string) $aplicacion['database'] : '');
        $migracion->setTipo(is_scalar($aplicacion['tipo'] ?? null) ? (string) $aplicacion['tipo'] : '');
        $migracion->setSha1(is_scalar($aplicacion['sha1'] ?? null) ? (string) $aplicacion['sha1'] : '');
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

    /**
     * @param array<string, mixed> $migracion
     * @return list<string>
     */
    private function suspenderSuscripcionesReplicacion(array $migracion): array
    {
        return $this->alterSuscripcionesReplicacion($migracion, false);
    }

    /**
     * @param array<string, mixed> $migracion
     * @return list<string>
     */
    private function reactivarSuscripcionesReplicacion(array $migracion): array
    {
        return $this->alterSuscripcionesReplicacion($migracion, true);
    }

    /**
     * @param array<string, mixed> $migracion
     * @return list<string>
     */
    private function avisosSuscripcionesSinReactivar(array $migracion): array
    {
        if (!$this->migracionEstructuraReplicada($migracion)) {
            return [];
        }

        $lines = ['    suscripciones NO reactivadas: corrija el error, avance el slot en el publicador y ejecute ENABLE + REFRESH PUBLICATION manualmente.'];
        foreach ($this->modulosReplicacionDeMigracion($migracion) as $modulo) {
            $subNombre = $this->nombreSuscripcion($modulo);
            if ($subNombre !== null) {
                $lines[] = sprintf('      - %s (modulo %s)', $subNombre, $modulo);
            }
        }

        return $lines;
    }

    /**
     * Pausa/reactiva suscripciones lógicas antes/después de migraciones de estructura en comun/sv-e.
     * Evita «falta la columna replicada» mientras el publicador migra y la réplica aún no.
     *
     * @param array<string, mixed> $migracion
     * @return list<string>
     */
    private function alterSuscripcionesReplicacion(array $migracion, bool $reactivar): array
    {
        if (!$this->migracionEstructuraReplicada($migracion)) {
            return [];
        }

        $lines = [];
        foreach ($this->modulosReplicacionDeMigracion($migracion) as $modulo) {
            $lines = array_merge($lines, $this->alterSuscripcionModulo($modulo, $reactivar));
        }

        return $lines;
    }

    /**
     * @param array<string, mixed> $migracion
     */
    private function migracionEstructuraReplicada(array $migracion): bool
    {
        foreach (self::normalizeRows($migracion['aplicaciones'] ?? []) as $aplicacion) {
            if (($aplicacion['tipo'] ?? '') !== MigracionTipo::ESTRUCTURA) {
                continue;
            }
            $databaseArchivo = $this->toScalarString($aplicacion['database_archivo'] ?? null);
            if (in_array($databaseArchivo, ['comun', 'sv-e'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $migracion
     * @return list<string>
     */
    private function modulosReplicacionDeMigracion(array $migracion): array
    {
        $modulos = [];
        foreach (self::normalizeRows($migracion['aplicaciones'] ?? []) as $aplicacion) {
            $archivoVal = $aplicacion['database_archivo'] ?? '';
            $archivo = is_scalar($archivoVal) ? (string) $archivoVal : '';
            if ($archivo === 'comun' || $archivo === 'sv-e') {
                $modulos[$archivo] = true;
            }
        }

        return array_keys($modulos);
    }

    /**
     * @return list<string>
     */
    private function alterSuscripcionModulo(string $modulo, bool $reactivar): array
    {
        if ($this->debeOmitirSuscripcionesReplicacion()) {
            return [];
        }

        $subNombre = $this->nombreSuscripcion($modulo);
        if ($subNombre === null) {
            return [];
        }

        $databaseSelect = match ($modulo) {
            'comun' => MigracionDatabase::COMUN_SELECT,
            'sv-e' => MigracionDatabase::SV_E_SELECT,
            default => null,
        };
        if ($databaseSelect === null) {
            return [];
        }

        try {
            $pdo = $this->connect($databaseSelect);
            if (!$this->suscripcionExiste($pdo, $subNombre)) {
                return [
                    sprintf(
                        '    suscripcion %s: no existe en %s; omitido',
                        $subNombre,
                        $databaseSelect,
                    ),
                ];
            }

            if ($reactivar) {
                $lines = $this->avanzarSlotSuscripcionEnPublicador($modulo, $subNombre);
                $this->execSqlScript($pdo, 'ALTER SUBSCRIPTION ' . $subNombre . ' ENABLE');
                $this->execSqlScript($pdo, 'ALTER SUBSCRIPTION ' . $subNombre . ' REFRESH PUBLICATION');
                $lines[] = sprintf('    suscripcion %s reactivada (ENABLE + REFRESH PUBLICATION)', $subNombre);

                return $lines;
            }

            $this->execSqlScript($pdo, 'ALTER SUBSCRIPTION ' . $subNombre . ' DISABLE');

            return [sprintf('    suscripcion %s pausada (DISABLE) durante migracion de estructura', $subNombre)];
        } catch (Throwable $e) {
            return [sprintf('    aviso suscripcion %s: %s', $subNombre, $e->getMessage())];
        }
    }

    /**
     * Descarta WAL pendiente incompatible (p. ej. renombres de columna) antes de reactivar la suscripcion.
     * El slot vive en el publicador (comun / sv-e), no en *_select.
     *
     * @return list<string>
     */
    private function avanzarSlotSuscripcionEnPublicador(string $modulo, string $subNombre): array
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $subNombre)) {
            return [sprintf('    aviso slot %s: nombre no valido', $subNombre)];
        }

        $databasePublicador = match ($modulo) {
            'comun' => MigracionDatabase::COMUN,
            'sv-e' => MigracionDatabase::SV_E,
            default => null,
        };
        if ($databasePublicador === null) {
            return [];
        }

        try {
            $pdo = $this->connect($databasePublicador);
            $stmt = $pdo->query(
                'SELECT 1 FROM pg_replication_slots WHERE slot_name = '
                . $pdo->quote($subNombre)
                . ' LIMIT 1',
            );
            if ($stmt === false || $stmt->fetchColumn() === false) {
                return [
                    sprintf(
                        '    slot %s no existe en publicador (%s); omitido avance LSN',
                        $subNombre,
                        $databasePublicador,
                    ),
                ];
            }

            $this->execSqlScript(
                $pdo,
                'SELECT pg_replication_slot_advance('
                . $pdo->quote($subNombre)
                . ', pg_current_wal_lsn())',
            );

            return [
                sprintf(
                    '    slot %s avanzado al LSN actual en publicador (%s)',
                    $subNombre,
                    $databasePublicador,
                ),
            ];
        } catch (Throwable $e) {
            return [
                sprintf(
                    '    aviso slot %s en publicador (%s): %s',
                    $subNombre,
                    $databasePublicador,
                    $e->getMessage(),
                ),
            ];
        }
    }

    private function nombreSuscripcion(string $modulo): ?string
    {
        $entornoPruebas = ConfigGlobal::esEntornoPruebas();

        return match ($modulo) {
            'comun' => $entornoPruebas ? 'subpruebascomun' : 'subcomun',
            'sv-e' => $entornoPruebas ? 'subpruebassve' : 'subsve',
            default => null,
        };
    }

    private function debeOmitirSuscripcionesReplicacion(): bool
    {
        return is_readable('/.dockerenv');
    }

    private function suscripcionExiste(PDO $pdo, string $subNombre): bool
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $subNombre)) {
            return false;
        }

        $stmt = $pdo->query(
            'SELECT 1 FROM pg_subscription WHERE subname = '
            . $pdo->quote($subNombre)
            . ' LIMIT 1',
        );

        return $stmt !== false && $stmt->fetchColumn() !== false;
    }

    /**
     * @param mixed $value
     */
    private function toScalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param mixed $rows
     * @return array<int, array<string, mixed>>
     */
    private static function normalizeRows(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                /** @var array<string, mixed> $row */
                $normalized[] = $row;
            }
        }

        return $normalized;
    }
}
