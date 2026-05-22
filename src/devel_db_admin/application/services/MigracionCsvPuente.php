<?php

declare(strict_types=1);

namespace src\devel_db_admin\application\services;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;
use src\shared\config\ConfigGlobal;

/**
 * Export/import CSV en el servidor web (Apache/PHP), sin COPY en disco del postgres remoto.
 *
 * Directivas en ficheros .sql (comentarios):
 *   -- @orbix_export_csv: log/db/locales.csv
 *   -- @orbix_export_query_begin
 *   SELECT ...
 *   -- @orbix_export_query_end
 *
 *   -- @orbix_import_csv: log/db/locales.csv
 *   -- @orbix_import_into: publicv.tabla(col1, col2, ...)
 *   -- @orbix_import_here
 */
final class MigracionCsvPuente
{
    private const MARK_EXPORT_CSV = '@orbix_export_csv:';
    private const MARK_EXPORT_BEGIN = '@orbix_export_query_begin';
    private const MARK_EXPORT_END = '@orbix_export_query_end';
    private const MARK_IMPORT_CSV = '@orbix_import_csv:';
    private const MARK_IMPORT_INTO = '@orbix_import_into:';
    private const MARK_IMPORT_HERE = '@orbix_import_here';

    /**
     * @return array{
     *     export_query: string|null,
     *     export_path: string|null,
     *     import_path: string|null,
     *     import_table: string|null,
     *     import_columns: list<string>|null,
     *     sql_before_import: string,
     *     sql_after_import: string
     * }
     */
    public function parse(string $sql): array
    {
        $exportPath = $this->matchMarkerValue($sql, self::MARK_EXPORT_CSV);
        $exportQuery = $this->extractBlock($sql, self::MARK_EXPORT_BEGIN, self::MARK_EXPORT_END);
        $importPath = $this->matchMarkerValue($sql, self::MARK_IMPORT_CSV);
        $importInto = $this->matchMarkerValue($sql, self::MARK_IMPORT_INTO);

        [$sqlBeforeImport, $sqlAfterImport] = $this->splitAtMarker($sql, self::MARK_IMPORT_HERE);

        if ($exportQuery !== null) {
            $sqlBeforeImport = $this->removeBlock($sqlBeforeImport, self::MARK_EXPORT_BEGIN, self::MARK_EXPORT_END);
            $sqlAfterImport = $this->removeBlock($sqlAfterImport, self::MARK_EXPORT_BEGIN, self::MARK_EXPORT_END);
            $sqlBeforeImport = $this->removeMarkerLines($sqlBeforeImport, [self::MARK_EXPORT_CSV]);
            $sqlAfterImport = $this->removeMarkerLines($sqlAfterImport, [self::MARK_EXPORT_CSV]);
        }

        $sqlBeforeImport = $this->removeMarkerLines($sqlBeforeImport, [
            self::MARK_IMPORT_CSV,
            self::MARK_IMPORT_INTO,
            self::MARK_IMPORT_HERE,
        ]);
        $sqlAfterImport = $this->removeMarkerLines($sqlAfterImport, [
            self::MARK_IMPORT_CSV,
            self::MARK_IMPORT_INTO,
            self::MARK_IMPORT_HERE,
        ]);

        $importTable = null;
        $importColumns = null;
        if ($importInto !== null) {
            [$importTable, $importColumns] = $this->parseImportInto($importInto);
        }

        return [
            'export_query' => $exportQuery !== null && trim($exportQuery) !== '' ? trim($exportQuery) : null,
            'export_path' => $exportPath,
            'import_path' => $importPath,
            'import_table' => $importTable,
            'import_columns' => $importColumns,
            'sql_before_import' => trim($sqlBeforeImport),
            'sql_after_import' => trim($sqlAfterImport),
        ];
    }

    public function tieneExport(array $plan): bool
    {
        return $plan['export_query'] !== null && $plan['export_path'] !== null;
    }

    public function tieneImport(array $plan): bool
    {
        return $plan['import_path'] !== null
            && $plan['import_table'] !== null
            && $plan['import_columns'] !== null;
    }

    /**
     * @return list<string>
     */
    public function export(PDO $pdo, array $plan): array
    {
        $path = $this->resolveRelativePath((string) $plan['export_path']);
        $this->ensureWritableDirectory($path);
        $query = (string) $plan['export_query'];
        try {
            $stmt = $pdo->query($query);
        } catch (PDOException $e) {
            throw new RuntimeException('Export CSV: ' . $e->getMessage(), 0, $e);
        }
        if (!$stmt instanceof PDOStatement) {
            $info = $pdo->errorInfo();
            throw new RuntimeException(sprintf(
                'Export CSV (%s): %s',
                (string) ($info[0] ?? 'HY000'),
                (string) ($info[2] ?? 'consulta fallida'),
            ));
        }

        $handle = fopen($path, 'wb');
        if ($handle === false) {
            throw new RuntimeException(sprintf('No se puede escribir %s', $path));
        }

        $rows = 0;
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            fputcsv($handle, array_values($row));
            $rows++;
        }
        fclose($handle);

        return [
            sprintf('    CSV exportado en servidor web: %s (%d filas)', $path, $rows),
        ];
    }

    /**
     * @return list<string>
     */
    public function import(PDO $pdo, array $plan): array
    {
        $path = $this->resolveRelativePath((string) $plan['import_path']);
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('No se encuentra el CSV en el servidor web: %s', $path));
        }

        $table = (string) $plan['import_table'];
        /** @var list<string> $columns */
        $columns = $plan['import_columns'];
        $columnList = implode(', ', $columns);
        $placeholders = implode(', ', array_map(static fn (string $c): string => ':' . $c, $columns));
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $columnList, $placeholders);
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException(sprintf('No se puede preparar INSERT para %s', $table));
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException(sprintf('No se puede leer %s', $path));
        }

        $rows = 0;
        while ($data = fgetcsv($handle)) {
            if ($data === [null] || $data === false) {
                continue;
            }
            $params = [];
            foreach ($columns as $i => $column) {
                $value = $data[$i] ?? null;
                if ($value === '') {
                    $value = null;
                }
                $params[$column] = $value;
            }
            $stmt->execute($params);
            $rows++;
        }
        fclose($handle);

        return [
            sprintf('    CSV importado desde servidor web: %s (%d filas en %s)', $path, $rows, $table),
        ];
    }

    public function resolveRelativePath(string $relativePath): string
    {
        $relativePath = trim($relativePath);
        if ($relativePath === '') {
            throw new RuntimeException('Ruta CSV de migracion vacia');
        }
        if (str_starts_with($relativePath, '/')) {
            $absolute = $relativePath;
        } else {
            $absolute = rtrim($this->directorioOrbix(), '/') . '/' . ltrim($relativePath, '/');
        }

        return $absolute;
    }

    private function ensureWritableDirectory(string $absolutePath): void
    {
        $dir = dirname($absolutePath);
        if (is_dir($dir)) {
            return;
        }
        if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('No se puede crear el directorio %s', $dir));
        }
    }

    private function directorioOrbix(): string
    {
        if (isset(ConfigGlobal::$directorio) && ConfigGlobal::$directorio !== '') {
            return (string) ConfigGlobal::$directorio;
        }

        return dirname(__DIR__, 4);
    }

    private function matchMarkerValue(string $sql, string $marker): ?string
    {
        if (preg_match('/^--\s*' . preg_quote($marker, '/') . '\s*(.+)$/m', $sql, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    private function extractBlock(string $sql, string $begin, string $end): ?string
    {
        $pattern = '/^--\s*' . preg_quote($begin, '/') . '\s*$.*?^--\s*'
            . preg_quote($end, '/') . '\s*$/ms';
        if (preg_match($pattern, $sql, $matches) !== 1) {
            return null;
        }

        $block = preg_replace('/^--\s*' . preg_quote($begin, '/') . '\s*\R/ms', '', $matches[0], 1);
        $block = preg_replace('/^--\s*' . preg_quote($end, '/') . '\s*$/ms', '', $block ?? '', 1);

        return trim((string) $block);
    }

    private function removeBlock(string $sql, string $begin, string $end): string
    {
        $pattern = '/^--\s*' . preg_quote($begin, '/') . '\s*$.*?^--\s*'
            . preg_quote($end, '/') . '\s*$\R?/ms';

        return trim((string) preg_replace($pattern, '', $sql));
    }

    /**
     * @param list<string> $markers
     */
    private function removeMarkerLines(string $sql, array $markers): string
    {
        foreach ($markers as $marker) {
            $sql = (string) preg_replace('/^--\s*' . preg_quote($marker, '/') . '.*\R?/m', '', $sql);
        }

        return trim($sql);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitAtMarker(string $sql, string $marker): array
    {
        $parts = preg_split('/^--\s*' . preg_quote($marker, '/') . '\s*$\R?/m', $sql, 2);
        if (!is_array($parts) || count($parts) < 2) {
            return [trim($sql), ''];
        }

        return [trim($parts[0]), trim($parts[1])];
    }

    /**
     * @return array{0: string, 1: list<string>}
     */
    private function parseImportInto(string $spec): array
    {
        if (preg_match('/^([a-zA-Z0-9_."]+)\(([^)]+)\)$/', trim($spec), $matches) !== 1) {
            throw new RuntimeException(sprintf('Formato @orbix_import_into no valido: %s', $spec));
        }

        $table = trim($matches[1]);
        $columns = array_values(array_filter(array_map('trim', explode(',', $matches[2]))));

        if ($columns === []) {
            throw new RuntimeException(sprintf('Sin columnas en @orbix_import_into: %s', $spec));
        }

        return [$table, $columns];
    }
}
