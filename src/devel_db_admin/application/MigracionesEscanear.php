<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\devel_db_admin\application\services\MigracionSqlAnalyzer;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use src\devel_db_admin\domain\value_objects\MigracionTipo;

final class MigracionesEscanear
{
    private const FILE_PATTERN = '/^(\d{12})_([A-Za-z0-9_-]+)__(comun|sv-e|sv)\.sql$/';

    public function __construct(
        private readonly MigracionAplicadaRepositoryInterface $repository,
        private readonly ?string $migrationsDir = null,
        private readonly ?MigracionSqlAnalyzer $analyzer = null,
    ) {
    }

    /**
     * @return array{migraciones: array<int, array<string, mixed>>, warnings: list<string>}
     */
    public function escanear(): array
    {
        $this->repository->ensureTabla();

        $dir = $this->migrationsDir ?? $this->defaultMigrationsDir();
        $files = glob($dir . '/*.sql');
        $files = is_array($files) ? $files : [];
        sort($files, SORT_STRING);

        $aplicadas = $this->indexAplicadas($this->repository->aplicadas());
        $migraciones = [];
        $warnings = [];
        $analyzer = $this->analyzer ?? new MigracionSqlAnalyzer();

        foreach ($files as $path) {
            $fileName = basename($path);
            if (!preg_match(self::FILE_PATTERN, $fileName, $matches)) {
                $warnings[] = sprintf('Ignorado %s: nombre de migracion no valido', $fileName);
                continue;
            }

            $prefijo = $matches[1];
            $descripcion = $matches[2];
            $dbArchivo = $matches[3];
            $groupId = $prefijo . '_' . $descripcion;
            $sql = file_get_contents($path);
            if ($sql === false) {
                $warnings[] = sprintf('No se puede leer %s', $fileName);
                continue;
            }

            $tipo = $analyzer->tipoDe($sql)->value();
            $sha1 = sha1($sql);
            $usaComodin = $analyzer->usaComodin($sql);

            if (!isset($migraciones[$groupId])) {
                $migraciones[$groupId] = [
                    'id' => $groupId,
                    'prefijo' => $prefijo,
                    'descripcion' => $descripcion,
                    'files' => [],
                    'aplicaciones' => [],
                ];
            }

            $migraciones[$groupId]['files'][] = [
                'file' => $fileName,
                'path' => $path,
                'database_archivo' => $dbArchivo,
                'tipo' => $tipo,
                'sha1' => $sha1,
                'usa_comodin' => $usaComodin,
            ];

            foreach (self::destinosPara($dbArchivo, $tipo) as $databaseReal) {
                $aplicada = $aplicadas[$this->key($prefijo, $descripcion, $databaseReal)] ?? null;
                $migraciones[$groupId]['aplicaciones'][] = [
                    'id' => $groupId,
                    'prefijo' => $prefijo,
                    'descripcion' => $descripcion,
                    'file' => $fileName,
                    'path' => $path,
                    'database_archivo' => $dbArchivo,
                    'database' => $databaseReal,
                    'tipo' => $tipo,
                    'sha1' => $sha1,
                    'usa_comodin' => $usaComodin,
                    'aplicada' => $aplicada,
                ];
            }
        }

        foreach ($migraciones as &$migracion) {
            $migracion['aplicaciones'] = $this->ordenarAplicaciones($migracion['aplicaciones']);
            $migracion['estado'] = $this->estadoGrupo($migracion['aplicaciones']);
        }
        unset($migracion);

        ksort($migraciones, SORT_STRING);

        return [
            'migraciones' => array_values($migraciones),
            'warnings' => $warnings,
        ];
    }

    /**
     * @return list<string>
     */
    public static function destinosPara(string $databaseArchivo, string $tipo): array
    {
        if ($tipo === MigracionTipo::DATOS) {
            return [$databaseArchivo];
        }

        return match ($databaseArchivo) {
            MigracionDatabase::COMUN => [MigracionDatabase::COMUN_SELECT, MigracionDatabase::COMUN],
            MigracionDatabase::SV_E => [MigracionDatabase::SV_E_SELECT, MigracionDatabase::SV_E],
            default => [$databaseArchivo],
        };
    }

    public static function ordenDatabase(string $database): int
    {
        // Primario antes que réplica (_select): en replicación lógica el publicador debe
        // tener el esquema migrado antes que el suscriptor (evita «falta columna replicada»).
        return match ($database) {
            MigracionDatabase::COMUN => 10,
            MigracionDatabase::COMUN_SELECT => 20,
            MigracionDatabase::SV_E => 30,
            MigracionDatabase::SV_E_SELECT => 40,
            MigracionDatabase::SV => 50,
            default => 99,
        };
    }

    private function defaultMigrationsDir(): string
    {
        return dirname(__DIR__, 3) . '/db/migrations';
    }

    /**
     * @param array<int, MigracionAplicada> $aplicadas
     * @return array<string, MigracionAplicada>
     */
    private function indexAplicadas(array $aplicadas): array
    {
        $index = [];
        foreach ($aplicadas as $aplicada) {
            $index[$this->key($aplicada->getPrefijo(), $aplicada->getDescripcion(), $aplicada->getDatabase())] = $aplicada;
        }

        return $index;
    }

    private function key(string $prefijo, string $descripcion, string $database): string
    {
        return $prefijo . '|' . $descripcion . '|' . $database;
    }

    /**
     * @param array<int, array<string, mixed>> $aplicaciones
     * @return array<int, array<string, mixed>>
     */
    private function ordenarAplicaciones(array $aplicaciones): array
    {
        usort($aplicaciones, static function (array $a, array $b): int {
            return self::ordenDatabase((string) $a['database']) <=> self::ordenDatabase((string) $b['database'])
                ?: strcmp((string) $a['file'], (string) $b['file']);
        });

        return $aplicaciones;
    }

    /**
     * @param array<int, array<string, mixed>> $aplicaciones
     */
    private function estadoGrupo(array $aplicaciones): string
    {
        if ($aplicaciones === []) {
            return 'pendiente';
        }

        $ok = 0;
        $error = 0;
        $cambiado = 0;
        foreach ($aplicaciones as $aplicacion) {
            $aplicada = $aplicacion['aplicada'];
            if (!$aplicada instanceof MigracionAplicada) {
                continue;
            }
            if (!$aplicada->isOk()) {
                $error++;
                continue;
            }
            $ok++;
            if ($aplicada->getSha1() !== (string) $aplicacion['sha1']) {
                $cambiado++;
            }
        }

        if ($error > 0) {
            return 'error';
        }
        if ($cambiado > 0) {
            return 'cambiado';
        }
        if ($ok === count($aplicaciones)) {
            return 'aplicada';
        }
        if ($ok > 0) {
            return 'parcial';
        }

        return 'pendiente';
    }
}
