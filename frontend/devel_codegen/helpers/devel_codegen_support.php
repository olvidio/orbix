<?php

/**
 * Helpers compartidos del módulo frontend/devel_codegen.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';

/**
 * @return list<array{attnum: int, field: string, type: string, length: mixed, lengthvar: mixed, notnull: mixed}>
 */
function devel_codegen_sql_rows(\PDO $oDbl, string $sql): array
{
    $stmt = $oDbl->query($sql);
    if ($stmt === false) {
        return [];
    }
    $out = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        if (!is_array($row)) {
            continue;
        }
        $out[] = [
            'attnum' => tessera_imprimir_int($row['attnum'] ?? 0),
            'field' => tessera_imprimir_string($row['field'] ?? ''),
            'type' => tessera_imprimir_string($row['type'] ?? ''),
            'length' => $row['length'] ?? null,
            'lengthvar' => $row['lengthvar'] ?? null,
            'notnull' => $row['notnull'] ?? null,
        ];
    }

    return $out;
}

function devel_codegen_fetch_column(\PDO $oDbl, string $sql): string
{
    $stmt = $oDbl->query($sql);
    if ($stmt === false) {
        return '';
    }
    $value = $stmt->fetchColumn();

    return tessera_imprimir_string($value);
}

function devel_codegen_file_contents(string $path): string
{
    $content = file_get_contents($path);

    return is_string($content) ? $content : '';
}

function devel_codegen_preg_replace(string $pattern, string $replacement, string $subject): string
{
    $result = preg_replace($pattern, $replacement, $subject);

    return is_string($result) ? $result : $subject;
}

/**
 * @return array{schema: string, tabla: string, schema_sql: string}
 */
function devel_codegen_schema_table_parts(string $qTabla): array
{
    $schema = strtok($qTabla, '.');
    if (!is_string($schema) || $schema === $qTabla) {
        return ['schema' => 'public', 'tabla' => $qTabla, 'schema_sql' => ''];
    }
    $next = strtok('.');
    $tabla = is_string($next) ? $next : $qTabla;

    return [
        'schema' => $schema,
        'tabla' => $tabla,
        'schema_sql' => "and n.nspname='$schema' ",
    ];
}
