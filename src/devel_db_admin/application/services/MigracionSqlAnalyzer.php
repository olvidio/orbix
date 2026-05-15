<?php

declare(strict_types=1);

namespace src\devel_db_admin\application\services;

use src\devel_db_admin\domain\value_objects\MigracionTipo;

final class MigracionSqlAnalyzer
{
    private const DDL_PATTERN = '/\b(CREATE|ALTER|DROP|TRUNCATE|COMMENT|GRANT|REVOKE|RENAME)\b/i';
    private const DML_PATTERN = '/\b(INSERT|UPDATE|DELETE|COPY)\b|\bSELECT\s+INTO\b/i';

    public function tipoDe(string $sql): MigracionTipo
    {
        $clean = $this->stripCommentsAndStrings($sql);
        $hasDdl = preg_match(self::DDL_PATTERN, $clean) === 1;
        $hasDml = preg_match(self::DML_PATTERN, $clean) === 1;

        if ($hasDdl || !$hasDml) {
            return new MigracionTipo(MigracionTipo::ESTRUCTURA);
        }

        return new MigracionTipo(MigracionTipo::DATOS);
    }

    public function usaComodin(string $sql): bool
    {
        $clean = $this->stripCommentsAndStrings($sql);

        return preg_match('/(?<![\w])\*\s*\.\s*["A-Za-z_]/', $clean) === 1;
    }

    public function expandirComodin(string $sql, string $schema): string
    {
        $schemaSql = '"' . str_replace('"', '""', $schema) . '"';

        return (string) preg_replace('/(?<![\w])\*\s*\./', $schemaSql . '.', $sql);
    }

    private function stripCommentsAndStrings(string $sql): string
    {
        $sql = (string) preg_replace('/\/\*.*?\*\//s', ' ', $sql);
        $sql = (string) preg_replace('/--[^\r\n]*/', ' ', $sql);
        $sql = (string) preg_replace("/'(?:''|[^'])*'/", "''", $sql);
        $sql = (string) preg_replace('/"(?:\"\"|[^"])*"/', '""', $sql);

        return $sql;
    }
}
