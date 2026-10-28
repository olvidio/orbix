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
                'attnum' => \frontend\shared\helpers\PayloadCoercion::int($row['attnum'] ?? 0),
                'field' => \frontend\shared\helpers\PayloadCoercion::string($row['field'] ?? ''),
                'type' => \frontend\shared\helpers\PayloadCoercion::string($row['type'] ?? ''),
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

        return \frontend\shared\helpers\PayloadCoercion::string($value);
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
     * Nombre de `case` de enum (PascalCase) para un campo: `id_activ` → `IdActiv`.
     */
    public static function criteriaCaseName(string $campo): string
    {
        $partes = preg_split('/[^A-Za-z0-9]+/', $campo, -1, PREG_SPLIT_NO_EMPTY);
        $nombre = '';
        foreach ($partes !== false ? $partes : [] as $parte) {
            $nombre .= ucfirst($parte);
        }
        if ($nombre === '' || ctype_digit($nombre[0])) {
            $nombre = 'Campo' . $nombre;
        }

        return $nombre;
    }

    /**
     * Código fuente del enum `<Entidad>Field` (campos consultables con Criteria).
     *
     * @param list<string> $campos nombres de campo de la entidad (= columna)
     */
    public static function criteriaFieldEnumSource(string $grupo, string $clase, array $campos): string
    {
        $cases = '';
        $vistos = [];
        foreach ($campos as $campo) {
            $case = self::criteriaCaseName($campo);
            if (isset($vistos[$case])) {
                continue;
            }
            $vistos[$case] = true;
            $cases .= "    case $case = '$campo';\n";
        }

        return "<?php\n\n"
            . "namespace src\\$grupo\\domain\\criteria;\n\n"
            . "use src\\shared\\domain\\criteria\\CriteriaField;\n\n"
            . "/**\n"
            . " * Campos de {@see \\src\\$grupo\\domain\\entity\\$clase} por los que se puede buscar u ordenar.\n"
            . " * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.\n"
            . " */\n"
            . "enum {$clase}Field: string implements CriteriaField\n"
            . "{\n"
            . $cases
            . "}\n";
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
