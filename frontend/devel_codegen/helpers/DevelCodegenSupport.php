<?php

declare(strict_types=1);

namespace frontend\devel_codegen\helpers;

use frontend\shared\helpers\PayloadCoercion;
use PDO;

final class DevelCodegenSupport
{
    /**
     * @return list<array{attnum: int, field: string, type: string, length: mixed, lengthvar: mixed, notnull: mixed}>
     */
    public static function sqlRows(PDO $oDbl, string $sql): array
    {
        $stmt = $oDbl->query($sql);
        if ($stmt === false) {
            return [];
        }
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row)) {
                continue;
            }
            $out[] = [
                'attnum' => PayloadCoercion::int($row['attnum'] ?? 0),
                'field' => PayloadCoercion::string($row['field'] ?? ''),
                'type' => PayloadCoercion::string($row['type'] ?? ''),
                'length' => $row['length'] ?? null,
                'lengthvar' => $row['lengthvar'] ?? null,
                'notnull' => $row['notnull'] ?? null,
            ];
        }

        return $out;
    }

    public static function fetchColumn(PDO $oDbl, string $sql): string
    {
        $stmt = $oDbl->query($sql);
        if ($stmt === false) {
            return '';
        }
        $value = $stmt->fetchColumn();

        return PayloadCoercion::string($value);
    }

    public static function fileContents(string $path): string
    {
        $content = file_get_contents($path);

        return is_string($content) ? $content : '';
    }

    public static function pregReplace(string $pattern, string $replacement, string $subject): string
    {
        $result = preg_replace($pattern, $replacement, $subject);

        return is_string($result) ? $result : $subject;
    }

    /**
     * @return array{schema: string, tabla: string, schema_sql: string}
     */
    public static function schemaTableParts(string $qTabla): array
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
}
