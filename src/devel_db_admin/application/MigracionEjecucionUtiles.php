<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use PDOException;
use Throwable;

/**
 * Reglas de ejecución de migraciones SQL multi-esquema (comodín *.).
 */
final class MigracionEjecucionUtiles
{
    /**
     * Esquemas «resto»: convención de tablas distinta; se excluyen de la expansión *.
     */
    public static function esEsquemaResto(string $schema): bool
    {
        $s = strtolower($schema);

        return $s === 'resto' || $s === 'restov' || $s === 'restof';
    }

    /**
     * Esquemas raíz de región STGR en comun (H-H, M-M): no usan tablas *_dl propias
     * y quedan fuera del comodín * en migraciones multi-esquema.
     */
    public static function esEsquemaRegionStgrComun(string $schema): bool
    {
        return $schema === 'H-H' || $schema === 'M-M';
    }

    public static function esReplicaSelect(string $database): bool
    {
        return str_ends_with($database, '_select');
    }

    public static function tieneSqlEjecutable(string $sql): bool
    {
        $clean = (string) preg_replace('/--[^\r\n]*/', '', $sql);
        $clean = (string) preg_replace('/\/\*.*?\*\//s', '', $clean);

        return trim($clean) !== '';
    }

    /**
     * Divide un script SQL en sentencias respetando literales, comentarios y dollar-quotes.
     *
     * @return list<string>
     */
    public static function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $len = strlen($sql);
        $i = 0;
        $inSingleQuote = false;
        $dollarTag = null;

        while ($i < $len) {
            $char = $sql[$i];

            if ($inSingleQuote) {
                $current .= $char;
                if ($char === "'" && ($i + 1 >= $len || $sql[$i + 1] !== "'")) {
                    $inSingleQuote = false;
                } elseif ($char === "'" && $sql[$i + 1] === "'") {
                    $current .= $sql[$i + 1];
                    $i += 2;
                    continue;
                }
                $i++;
                continue;
            }

            if ($dollarTag !== null) {
                if ($char === '$') {
                    $close = '$' . $dollarTag . '$';
                    if (substr($sql, $i, strlen($close)) === $close) {
                        $current .= $close;
                        $i += strlen($close);
                        $dollarTag = null;
                        continue;
                    }
                }
                $current .= $char;
                $i++;
                continue;
            }

            if ($char === '-' && ($i + 1) < $len && $sql[$i + 1] === '-') {
                while ($i < $len && $sql[$i] !== "\n") {
                    $current .= $sql[$i];
                    $i++;
                }
                continue;
            }

            if ($char === '$') {
                $j = $i + 1;
                while ($j < $len && $sql[$j] !== '$' && (ctype_alnum($sql[$j]) || $sql[$j] === '_')) {
                    $j++;
                }
                if ($j < $len && $sql[$j] === '$') {
                    $dollarTag = substr($sql, $i + 1, $j - $i - 1);
                    $open = substr($sql, $i, $j - $i + 1);
                    $current .= $open;
                    $i = $j + 1;
                    continue;
                }
            }

            if ($char === "'") {
                $inSingleQuote = true;
                $current .= $char;
                $i++;
                continue;
            }

            if ($char === ';') {
                if (self::tieneSqlEjecutable($current)) {
                    $statements[] = $current;
                }
                $current = '';
                $i++;
                continue;
            }

            $current .= $char;
            $i++;
        }

        if (self::tieneSqlEjecutable($current)) {
            $statements[] = $current;
        }

        return $statements;
    }

    /**
     * PostgreSQL: SQLSTATE 3F000 = invalid_schema_name ("schema X does not exist").
     * No confundir con 42P01 (relación inexistente cuando el esquema sí existe).
     */
    public static function esErrorEsquemaInexistente(Throwable $e): bool
    {
        if (!$e instanceof PDOException) {
            return false;
        }

        return (string) ($e->errorInfo[0] ?? '') === '3F000';
    }

    /**
     * Comprueba si el namespace existe en la base actual (catalogo PostgreSQL).
     */
    public static function esquemaExisteEnPostgres(PDO $pdo, string $schema): bool
    {
        $sql = 'SELECT 1 FROM pg_catalog.pg_namespace WHERE nspname = ? LIMIT 1';
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            return false;
        }
        $stmt->execute([$schema]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Errores que en la practica equivalen a «este esquema no existe en esta BD» y pueden omitirse
     * al iterar el comodin, sin confundir con tabla mal nombrada en un esquema que si existe.
     */
    public static function esOmitiblePorAusenciaDeEsquema(Throwable $e, PDO $pdo, string $schema): bool
    {
        if (self::esErrorEsquemaInexistente($e)) {
            return true;
        }
        if (!$e instanceof PDOException) {
            return false;
        }
        if ((string) ($e->errorInfo[0] ?? '') !== '42P01') {
            return false;
        }

        return !self::esquemaExisteEnPostgres($pdo, $schema);
    }
}
