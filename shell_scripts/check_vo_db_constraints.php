#!/usr/bin/env php
<?php

declare(strict_types=1);

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;

require dirname(__DIR__) . '/apps/core/global_header.inc';

final class VoDbValidatorCli
{
    private const MODULE_ALL = 'all';
    private array $registry;
    private array $aliases;
    private array $pdoCache = [];

    public function __construct()
    {
        $registryData = require __DIR__ . '/vo_validation_registry.php';
        $this->registry = $registryData['modules'] ?? [];
        $this->aliases = $registryData['aliases'] ?? [];
    }

    public function run(array $argv): int
    {
        try {
            $options = $this->parseOptions();
            $report = $this->validateByModule($options);
            $this->printOutput($report, $options);
            $this->writeJsonReportIfNeeded($report, $options);
            return 0;
        } catch (InvalidArgumentException $exception) {
            fwrite(STDERR, "ERROR: {$exception->getMessage()}\n");
            $this->printUsage();
            return 2;
        } catch (Throwable $exception) {
            fwrite(STDERR, "ERROR: {$exception->getMessage()}\n");
            return 1;
        }
    }

    private function parseOptions(): array
    {
        $options = getopt('', [
            'module:',
            'esquema:',
            'esquemas:',
            'database:',
            'limit::',
            'from-id::',
            'max-batches::',
            'format::',
            'output::',
            'preflight-missing::',
            'help::',
        ]);

        if (isset($options['help'])) {
            $this->printUsage();
            exit(0);
        }

        $module = trim((string)($options['module'] ?? self::MODULE_ALL));
        $esquema = trim((string)($options['esquema'] ?? ''));
        $esquemasRaw = trim((string)($options['esquemas'] ?? ''));
        if ($esquema === '' && $esquemasRaw === '') {
            throw new InvalidArgumentException("Falta --esquema o --esquemas (ejemplo: H-dlbv,H-madv).");
        }

        $database = (string)($options['database'] ?? 'sv');
        $limit = isset($options['limit']) ? (int)$options['limit'] : 500;
        $fromId = isset($options['from-id']) ? (int)$options['from-id'] : 0;
        $maxBatches = isset($options['max-batches']) ? (int)$options['max-batches'] : 0;
        $format = (string)($options['format'] ?? 'text');
        $output = (string)($options['output'] ?? 'documentacion/vo_db_validation_report.json');
        $preflightMissing = (string)($options['preflight-missing'] ?? '');

        if ($limit < 1) {
            throw new InvalidArgumentException('--limit debe ser mayor que 0.');
        }
        if ($fromId < 0) {
            throw new InvalidArgumentException('--from-id no puede ser negativo.');
        }
        if ($maxBatches < 0) {
            throw new InvalidArgumentException('--max-batches no puede ser negativo.');
        }
        if (!in_array($format, ['text', 'json'], true)) {
            throw new InvalidArgumentException("--format debe ser 'text' o 'json'.");
        }

        $schemas = $this->parseSchemas($esquema, $esquemasRaw);

        return [
            'module' => $this->normalizeModule($module),
            'schemas' => $schemas,
            'database' => $database,
            'limit' => $limit,
            'from_id' => $fromId,
            'max_batches' => $maxBatches,
            'format' => $format,
            'output' => $output,
            'preflight_missing' => $preflightMissing,
        ];
    }

    private function normalizeModule(string $module): string
    {
        $normalized = strtolower(trim($module));
        if ($normalized === '') {
            return self::MODULE_ALL;
        }
        return $this->aliases[$normalized] ?? $normalized;
    }

    private function validateByModule(array $options): array
    {
        $effectiveRegistry = $this->buildEffectiveRegistry($options['module']);
        if (empty($effectiveRegistry)) {
            throw new InvalidArgumentException('No hay módulos configurados ni autodetectados para validar.');
        }

        $selectedModules = $this->resolveSelectedModules($options['module'], $effectiveRegistry);
        $missingTables = $this->collectMissingTablesBySchema($selectedModules, $effectiveRegistry, $options);
        if ($options['preflight_missing'] !== '') {
            $this->writePreflightMissingTables($missingTables, $options['preflight_missing']);
        }
        $byModule = [];
        $allIncidents = [];
        $invalidByField = [];
        $rowsChecked = 0;
        $rowsWithErrors = 0;

        foreach ($selectedModules as $moduleName) {
            $moduleConfig = $effectiveRegistry[$moduleName];
            $byModule[$moduleName] = $this->validateSingleModuleAcrossSchemas($moduleName, $moduleConfig, $options);
            $modSummary = $byModule[$moduleName]['summary'];
            $rowsChecked += $modSummary['rows_checked'];
            $rowsWithErrors += $modSummary['rows_with_errors'];
            $allIncidents = [...$allIncidents, ...$byModule[$moduleName]['incidents']];
            foreach ($modSummary['invalid_by_field'] as $field => $count) {
                $invalidByField[$field] = ($invalidByField[$field] ?? 0) + $count;
            }
        }

        ksort($invalidByField);

        return [
            'module' => $options['module'],
            'mode' => 'exhaustive',
            'execution' => [
                'schemas' => $options['schemas'],
                'database' => $options['database'],
                'limit' => $options['limit'],
                'from_id' => $options['from_id'],
                'max_batches' => $options['max_batches'],
                'checked_at' => date(DATE_ATOM),
            ],
            'summary' => [
                'modules_checked' => count($selectedModules),
                'modules_available' => count($effectiveRegistry),
                'schemas_checked' => count($options['schemas']),
                'missing_tables_found' => count($missingTables),
                'rows_checked' => $rowsChecked,
                'rows_with_errors' => $rowsWithErrors,
                'incidents_found' => count($allIncidents),
                'invalid_by_field' => $invalidByField,
            ],
            'missing_tables' => $missingTables,
            'by_module' => $byModule,
            'incidents' => $allIncidents,
        ];
    }

    private function collectMissingTablesBySchema(array $selectedModules, array $registry, array $options): array
    {
        $missing = [];
        foreach ($options['schemas'] as $schema) {
            foreach ($selectedModules as $moduleName) {
                foreach ($registry[$moduleName]['tables'] as $tableCfg) {
                    $table = (string)$tableCfg['table'];
                    $connectionKey = (string)($tableCfg['connection_key'] ?? 'oDB');
                    $pdo = $this->getPdoForConnectionKey($connectionKey, $schema, $options['database']);
                    if (!$this->tableExists($pdo, $table)) {
                        $missing[] = [
                            'schema' => $schema,
                            'module' => $moduleName,
                            'table' => $table,
                            'entity' => (string)$tableCfg['entity'],
                            'connection_key' => $connectionKey,
                        ];
                    }
                }
            }
        }
        return $missing;
    }

    private function tableExists(PDO $pdo, string $table): bool
    {
        $sql = "SELECT to_regclass(:table_name)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':table_name', $table, PDO::PARAM_STR);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        return $value !== false && $value !== null;
    }

    private function writePreflightMissingTables(array $missingTables, string $outputPathRaw): void
    {
        $root = dirname(__DIR__);
        $outputPath = $root . '/' . ltrim($outputPathRaw, '/');
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $payload = [
            'missing_tables_found' => count($missingTables),
            'missing_tables' => $missingTables,
        ];
        file_put_contents($outputPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function buildEffectiveRegistry(string $requestedModule): array
    {
        $autoRegistry = $this->discoverModulesFromRepositories($requestedModule === self::MODULE_ALL ? null : $requestedModule);
        $effective = $autoRegistry;

        // Manual registry has priority over autodiscovery for same module
        foreach ($this->registry as $module => $cfg) {
            $effective[$module] = $cfg;
        }

        ksort($effective);
        return $effective;
    }

    private function discoverModulesFromRepositories(?string $onlyModule = null): array
    {
        $repoFiles = glob(dirname(__DIR__) . '/src/*/infrastructure/persistence/postgresql/Pg*Repository.php');
        if (!is_array($repoFiles)) {
            return [];
        }

        $modules = [];
        foreach ($repoFiles as $repoFile) {
            if (!preg_match('#/src/([^/]+)/infrastructure/persistence/postgresql/#', $repoFile, $modMatch)) {
                continue;
            }
            $module = $modMatch[1];
            if ($onlyModule !== null && $module !== $onlyModule) {
                continue;
            }

            $content = @file_get_contents($repoFile);
            if (!is_string($content) || $content === '') {
                continue;
            }

            if (!preg_match("/setNomTabla\\('([^']+)'\\)/", $content, $tableMatch)) {
                continue;
            }
            $table = $tableMatch[1];
            $connectionKey = 'oDB';
            if (preg_match('/\$oDbl\s*=\s*\$GLOBALS\[[\'"]([^\'"]+)[\'"]\]/', $content, $dbMatch)) {
                $connectionKey = $dbMatch[1];
            }

            $useMap = [];
            if (preg_match_all('/^use\\s+([^;]+);/m', $content, $useMatches)) {
                foreach ($useMatches[1] as $useFqcn) {
                    $useFqcn = trim($useFqcn);
                    $short = substr($useFqcn, strrpos($useFqcn, '\\') + 1);
                    $useMap[$short] = '\\' . ltrim($useFqcn, '\\');
                }
            }

            if (!preg_match('/([A-Za-z_][A-Za-z0-9_]*)::fromArray\\(/', $content, $entityMatch)) {
                continue;
            }
            $entityShort = $entityMatch[1];
            $entityClass = $useMap[$entityShort] ?? null;
            if ($entityClass === null || !class_exists($entityClass)) {
                continue;
            }

            $modules[$module]['tables'][] = [
                'table' => $table,
                'entity' => $entityClass,
                'connection_key' => $connectionKey,
            ];
        }

        // Dedupe tables by name per module
        foreach ($modules as $module => $cfg) {
            $seen = [];
            $deduped = [];
            foreach ($cfg['tables'] as $tableCfg) {
                $key = $tableCfg['table'] . '|' . ($tableCfg['connection_key'] ?? 'oDB');
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $deduped[] = $tableCfg;
            }
            $modules[$module]['tables'] = $deduped;
        }

        return $modules;
    }

    private function resolveSelectedModules(string $requestedModule, array $registry): array
    {
        if ($requestedModule === self::MODULE_ALL) {
            $modules = array_keys($registry);
            sort($modules);
            return $modules;
        }
        if (!isset($registry[$requestedModule])) {
            $available = implode(', ', array_keys($registry));
            throw new InvalidArgumentException("Módulo no soportado: {$requestedModule}. Disponibles: {$available}");
        }
        return [$requestedModule];
    }

    private function parseSchemas(string $singleSchema, string $schemasRaw): array
    {
        $schemas = [];
        if ($schemasRaw !== '') {
            foreach (explode(',', $schemasRaw) as $schema) {
                $schema = trim($schema);
                if ($schema !== '') {
                    $schemas[] = $schema;
                }
            }
        } elseif ($singleSchema !== '') {
            $schemas[] = $singleSchema;
        }

        $schemas = array_values(array_unique($schemas));
        if ($schemas === []) {
            throw new InvalidArgumentException('No se detectaron esquemas válidos.');
        }
        return $schemas;
    }

    private function validateSingleModuleAcrossSchemas(string $moduleName, array $moduleConfig, array $options): array
    {
        $bySchema = [];
        $rowsChecked = 0;
        $rowsWithErrors = 0;
        $incidents = [];
        $invalidByField = [];
        $tablesCount = count($moduleConfig['tables'] ?? []);

        foreach ($options['schemas'] as $schema) {
            $schemaReport = $this->validateSingleModuleSingleSchema($moduleName, $moduleConfig, $schema, $options);
            $bySchema[$schema] = $schemaReport;

            $summary = $schemaReport['summary'];
            $rowsChecked += $summary['rows_checked'];
            $rowsWithErrors += $summary['rows_with_errors'];
            $incidents = [...$incidents, ...$schemaReport['incidents']];
            foreach ($summary['invalid_by_field'] as $field => $count) {
                $invalidByField[$field] = ($invalidByField[$field] ?? 0) + $count;
            }
        }

        ksort($invalidByField);

        return [
            'module' => $moduleName,
            'mode' => 'exhaustive',
            'summary' => [
                'schemas_checked' => count($options['schemas']),
                'tables_checked_per_schema' => $tablesCount,
                'rows_checked' => $rowsChecked,
                'rows_with_errors' => $rowsWithErrors,
                'incidents_found' => count($incidents),
                'invalid_by_field' => $invalidByField,
            ],
            'by_schema' => $bySchema,
            'incidents' => $incidents,
        ];
    }

    private function validateSingleModuleSingleSchema(string $moduleName, array $moduleConfig, string $schema, array $options): array
    {
        $tablesReport = [];
        $incidents = [];
        $invalidByField = [];
        $rowsChecked = 0;
        $rowsWithErrors = 0;

        foreach ($moduleConfig['tables'] as $tableCfg) {
            $tableReport = $this->validateTableExhaustive(
                (string)$tableCfg['table'],
                (string)$tableCfg['entity'],
                $options,
                $moduleName,
                $schema,
                (string)($tableCfg['connection_key'] ?? 'oDB')
            );
            $tablesReport[$tableCfg['table']] = $tableReport;
            $rowsChecked += $tableReport['rows_checked'];
            $rowsWithErrors += $tableReport['rows_with_errors'];
            $incidents = [...$incidents, ...$tableReport['incidents']];
            foreach ($tableReport['invalid_by_field'] as $field => $count) {
                $invalidByField[$field] = ($invalidByField[$field] ?? 0) + $count;
            }
        }

        ksort($invalidByField);

        return [
            'module' => $moduleName,
            'schema' => $schema,
            'summary' => [
                'tables_checked' => count($moduleConfig['tables']),
                'rows_checked' => $rowsChecked,
                'rows_with_errors' => $rowsWithErrors,
                'incidents_found' => count($incidents),
                'invalid_by_field' => $invalidByField,
            ],
            'tables' => $tablesReport,
            'incidents' => $incidents,
        ];
    }

    private function validateTableExhaustive(
        string $table,
        string $entityClass,
        array $options,
        string $moduleName,
        string $schema,
        string $connectionKey
    ): array
    {
        $pdo = $this->getPdoForConnectionKey($connectionKey, $schema, $options['database']);
        $primaryKey = 'id_item';
        $tableError = null;
        try {
            /** @var object $entityInstance */
            $entityInstance = $this->instantiateEntitySafely($entityClass);
            if (method_exists($entityInstance, 'getPrimary_key')) {
                $primaryKey = (string)$entityInstance->getPrimary_key();
            }
        } catch (Throwable $exception) {
            $tableError = "Entidad no instanciable en CLI: {$exception->getMessage()}";
        }

        if ($tableError !== null) {
            return [
                'module' => $moduleName,
                'schema' => $schema,
                'entity' => $entityClass,
                'table' => $table,
                'connection_key' => $connectionKey,
                'primary_key' => $primaryKey,
                'mode' => 'exhaustive',
                'rows_checked' => 0,
                'rows_with_errors' => 0,
                'incidents_found' => 0,
                'invalid_by_field' => [],
                'last_id_checked' => $options['from_id'],
                'batches_processed' => 0,
                'table_error' => $tableError,
                'incidents' => [],
            ];
        }

        $voMap = $this->extractVoSettersMap($entityClass);

        $sql = <<<SQL
SELECT *
FROM {$table}
WHERE {$primaryKey} > :from_id
ORDER BY {$primaryKey}
LIMIT :limit
SQL;
        $incidents = [];
        $invalidByField = [];
        $rowsChecked = 0;
        $lastId = $options['from_id'];
        $batches = 0;
        $rowsWithErrorsByPk = [];
        $tableError = null;

        while (true) {
            if ($options['max_batches'] > 0 && $batches >= $options['max_batches']) {
                break;
            }

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':from_id', $lastId, PDO::PARAM_INT);
                $stmt->bindValue(':limit', $options['limit'], PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $exception) {
                $tableError = $exception->getMessage();
                break;
            }
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                $rowIncidents = $this->validateRowWithEntityAndVoMap($row, $table, $primaryKey, $entityClass, $voMap, $moduleName);
                if ($rowIncidents !== []) {
                    foreach ($rowIncidents as $incident) {
                        $field = $incident['campo'];
                        $invalidByField[$field] = ($invalidByField[$field] ?? 0) + 1;
                        $incidents[] = $incident;
                        $rowsWithErrorsByPk[(string)$incident['primary_key']] = true;
                    }
                }
                $rowsChecked++;
                $lastId = (int)$row[$primaryKey];
            }
            $batches++;
        }

        ksort($invalidByField);

        return [
            'module' => $moduleName,
            'schema' => $schema,
            'entity' => $entityClass,
            'table' => $table,
            'connection_key' => $connectionKey,
            'primary_key' => $primaryKey,
            'mode' => 'exhaustive',
            'rows_checked' => $rowsChecked,
            'rows_with_errors' => count($rowsWithErrorsByPk),
            'incidents_found' => count($incidents),
            'invalid_by_field' => $invalidByField,
            'last_id_checked' => $lastId,
            'batches_processed' => $batches,
            'table_error' => $tableError,
            'incidents' => $incidents,
        ];
    }

    private function extractVoSettersMap(string $entityClass): array
    {
        $map = [];
        $reflection = new ReflectionClass($entityClass);
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if (!str_starts_with($name, 'set') || !str_ends_with($name, 'Vo')) {
                continue;
            }
            $fieldPascal = substr($name, 3, -2);
            $fieldSnake = $this->pascalToSnake($fieldPascal);
            $map[$fieldSnake] = $name;
        }
        return $map;
    }

    private function validateRowWithEntityAndVoMap(
        array $row,
        string $table,
        string $primaryKey,
        string $entityClass,
        array $voMap,
        ?string $moduleName = null
    ): array {
        $incidents = [];
        $pkValue = $row[$primaryKey] ?? null;
        $base = [
            'module' => $moduleName,
            'tabla' => $table,
            'primary_key' => $pkValue,
            'entity' => $entityClass,
        ];

        try {
            $normalizedRow = $this->normalizeRowForEntityHydration($entityClass, $row, $voMap);
            $entityClass::fromArray($normalizedRow);
        } catch (Throwable $exception) {
            $incidents[] = $base + [
                'campo' => '_entity',
                'valor_actual' => null,
                'motivo' => "Hidratación entidad falló: {$exception->getMessage()}",
                'sql_sugerido' => null,
            ];
        }

        foreach ($voMap as $field => $setter) {
            try {
                $entity = $this->instantiateEntitySafely($entityClass);
                $value = $row[$field] ?? null;
                $coercedValue = $this->coerceValueForSetter($entityClass, $setter, $value);
                $entity->{$setter}($coercedValue);
            } catch (Throwable $exception) {
                $incidents[] = $base + [
                    'campo' => $field,
                    'valor_actual' => $row[$field] ?? null,
                    'motivo' => $exception->getMessage(),
                    'sql_sugerido' => $this->buildSqlSuggestion($table, $primaryKey, (string)$pkValue, $field, $row[$field] ?? null, $exception->getMessage()),
                ];
            }
        }

        return $incidents;
    }

    private function instantiateEntitySafely(string $entityClass): object
    {
        set_error_handler(
            static function (int $severity, string $message, string $file, int $line): bool {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }
        );
        try {
            return new $entityClass();
        } finally {
            restore_error_handler();
        }
    }

    private function getPdoForConnectionKey(string $connectionKey, string $inputSchema, string $defaultDatabase): PDO
    {
        [$database, $schema] = $this->resolveDbAndSchemaByConnectionKey($connectionKey, $inputSchema, $defaultDatabase);
        $cacheKey = $database . '|' . $schema;
        if (isset($this->pdoCache[$cacheKey])) {
            return $this->pdoCache[$cacheKey];
        }

        ConfigGlobal::setTest_mode(true);
        $configDB = new ConfigDB($database);
        $config = $configDB->getEsquema($schema);
        $pdo = (new DBConnection($config))->getPDO();
        $this->pdoCache[$cacheKey] = $pdo;
        return $pdo;
    }

    private function resolveDbAndSchemaByConnectionKey(string $connectionKey, string $inputSchema, string $defaultDatabase): array
    {
        $suffix = $this->extractSchemaSuffix($inputSchema);
        $baseSchema = $this->stripSchemaSuffix($inputSchema);

        return match ($connectionKey) {
            'oDB' => [$defaultDatabase, $inputSchema],
            'oDBP' => [$defaultDatabase, 'public' . $suffix],
            'oDBR' => [$defaultDatabase, 'resto' . $suffix],
            'oDBC' => ['comun', $baseSchema],
            'oDBPC' => ['comun', 'public'],
            'oDBRC' => ['comun', 'resto'],
            'oDBE' => ['sv-e', $inputSchema],
            'oDBEP' => ['sv-e', 'public' . $suffix],
            'oDBER' => ['sv-e', 'resto' . $suffix],
            'oDBC_Select' => ['comun_select', $baseSchema],
            'oDBPC_Select' => ['comun_select', 'public'],
            'oDBRC_Select' => ['comun_select', 'resto'],
            'oDBE_Select' => ['sv-e_select', $inputSchema],
            'oDBEP_Select' => ['sv-e_select', 'public' . $suffix],
            'oDBER_Select' => ['sv-e_select', 'resto' . $suffix],
            default => [$defaultDatabase, $inputSchema],
        };
    }

    private function extractSchemaSuffix(string $schema): string
    {
        if ($schema === '') {
            return '';
        }
        $last = substr($schema, -1);
        return in_array($last, ['v', 'f'], true) ? $last : '';
    }

    private function stripSchemaSuffix(string $schema): string
    {
        $suffix = $this->extractSchemaSuffix($schema);
        if ($suffix === '') {
            return $schema;
        }
        return substr($schema, 0, -1);
    }

    private function normalizeRowForEntityHydration(string $entityClass, array $row, array $voMap): array
    {
        foreach ($row as $field => $value) {
            $setter = 'set' . ucfirst($field);
            if (!method_exists($entityClass, $setter) && isset($voMap[$field])) {
                $setter = $voMap[$field];
            }
            if (!method_exists($entityClass, $setter)) {
                continue;
            }
            $row[$field] = $this->coerceValueForSetter($entityClass, $setter, $value);
        }
        return $row;
    }

    private function coerceValueForSetter(string $entityClass, string $setter, mixed $value): mixed
    {
        try {
            $method = new ReflectionMethod($entityClass, $setter);
        } catch (ReflectionException) {
            return $value;
        }

        $params = $method->getParameters();
        if ($params === []) {
            return $value;
        }
        $type = $params[0]->getType();
        if (!$type instanceof ReflectionNamedType) {
            return $value;
        }

        $typeName = $type->getName();
        if ($value === null || $value === '') {
            return $value;
        }

        if ($typeName === 'int' && is_numeric($value)) {
            return (int)$value;
        }
        if ($typeName === 'float' && is_numeric($value)) {
            return (float)$value;
        }
        if ($typeName === 'bool') {
            return $this->coerceBool($value);
        }
        if ($typeName === \src\shared\domain\value_objects\DateTimeLocal::class && is_string($value)) {
            $normalized = trim($value);
            if ($normalized === '') {
                return null;
            }
            return new \src\shared\domain\value_objects\DateTimeLocal($normalized);
        }

        return $value;
    }

    private function coerceBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $normalized = strtolower((string)$value);
        return in_array($normalized, ['1', 't', 'true', 'y', 'yes'], true);
    }

    private function pascalToSnake(string $input): string
    {
        $snake = (string)preg_replace('/(?<!^)[A-Z]/', '_$0', $input);
        return strtolower($snake);
    }

    private function buildSqlSuggestion(
        string $table,
        string $primaryKey,
        string $pkValue,
        string $field,
        mixed $rawValue,
        string $message
    ): ?string {
        if ($rawValue === null) {
            return null;
        }

        if (is_string($rawValue)) {
            $trimmed = trim($rawValue);
            if ($trimmed === '' || str_contains(mb_strtolower($message), 'vacío')) {
                return "UPDATE {$table} SET {$field} = NULL WHERE {$primaryKey} = {$pkValue};";
            }
            if (preg_match('/m[aá]ximo\s+(\d+)\s+caracteres/i', $message, $matches)) {
                $maxLen = (int)$matches[1];
                return "UPDATE {$table} SET {$field} = LEFT(TRIM({$field}), {$maxLen}) WHERE {$primaryKey} = {$pkValue};";
            }
            if (str_contains(mb_strtolower($message), 'invalid value')) {
                return "UPDATE {$table} SET {$field} = NULL WHERE {$primaryKey} = {$pkValue};";
            }
        }

        if (is_numeric($rawValue) && str_contains(mb_strtolower($message), 'must be')) {
            return null;
        }

        return null;
    }

    private function printOutput(array $report, array $options): void
    {
        if ($options['format'] === 'json') {
            echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
            return;
        }

        $summary = $report['summary'];
        echo "module={$report['module']}\n";
        echo "mode={$report['mode']}\n";
        echo "modules_checked={$summary['modules_checked']}\n";
        echo "schemas_checked={$summary['schemas_checked']}\n";
        echo "missing_tables_found={$summary['missing_tables_found']}\n";
        echo "rows_checked={$summary['rows_checked']}\n";
        echo "rows_with_errors={$summary['rows_with_errors']}\n";
        echo "incidents_found={$summary['incidents_found']}\n";
        echo "invalid_by_field=" . json_encode($summary['invalid_by_field'], JSON_UNESCAPED_UNICODE) . "\n";
        foreach ($report['by_module'] as $module => $moduleReport) {
            echo "module={$module} schemas_checked={$moduleReport['summary']['schemas_checked']} rows_checked={$moduleReport['summary']['rows_checked']} incidents={$moduleReport['summary']['incidents_found']}\n";
            foreach ($moduleReport['by_schema'] as $schema => $schemaReport) {
                echo "  schema={$schema} rows_checked={$schemaReport['summary']['rows_checked']} incidents={$schemaReport['summary']['incidents_found']}\n";
                foreach ($schemaReport['tables'] as $table => $tableReport) {
                    echo "    table={$table} rows_checked={$tableReport['rows_checked']} incidents={$tableReport['incidents_found']} batches={$tableReport['batches_processed']} last_id={$tableReport['last_id_checked']}\n";
                }
            }
        }

        foreach ($report['incidents'] as $incident) {
            $value = var_export($incident['valor_actual'], true);
            $sql = $incident['sql_sugerido'] ?? '(sin sugerencia segura)';
            echo "[id_item={$incident['primary_key']}] campo={$incident['campo']} valor={$value}\n";
            echo "  motivo: {$incident['motivo']}\n";
            echo "  sql_sugerido: {$sql}\n";
        }
    }

    private function writeJsonReportIfNeeded(array $report, array $options): void
    {
        $root = dirname(__DIR__);
        $outputPath = $root . '/' . ltrim($options['output'], '/');
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        file_put_contents($outputPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($options['format'] === 'text') {
            echo "report={$outputPath}\n";
        }
    }

    private function printUsage(): void
    {
        $usage = <<<TXT
Uso:
  php shell_scripts/check_vo_db_constraints.php --module=profesores --esquema=H-dlbv [opciones]
  php shell_scripts/check_vo_db_constraints.php --module=all --esquemas=H-dlbv,H-madv [opciones]

Opciones:
  --module=...     Carpeta/módulo o all. Default: all
  --esquema=...    Esquema PostgreSQL único (ejemplo: H-dlbv)
  --esquemas=...   Lista de esquemas separados por coma (ej: H-dlbv,H-madv)
  --database=...   Dataset de ConfigDB. Default: sv
  --limit=...      Tamaño de lote por iteración. Default: 500
  --from-id=...    Revisar filas con id_item > from-id. Default: 0
  --max-batches=... Limita lotes (0=sin límite, exhaustivo). Default: 0
  --format=...     text|json. Default: text
  --output=...     Ruta del informe JSON relativo al repo.
  --preflight-missing=... JSON con tablas inexistentes detectadas por esquema antes del barrido.
  --help           Muestra esta ayuda
TXT;
        echo $usage . PHP_EOL;
    }
}

$app = new VoDbValidatorCli();
exit($app->run($argv));
