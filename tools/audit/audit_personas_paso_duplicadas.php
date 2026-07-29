#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * @deprecated Preferir tools/audit/audit_personas_paso_limpieza.php
 *             y tools/audit/sql/personas_paso_limpieza.sql (plan por buckets).
 *
 * Auditoría: personas de paso (`p_de_paso_ex`) que ya existen como personas
 * permanentes en Orbix (`global.personas`), y datos asociados antes de borrar.
 *
 * Solo lectura. Matching por defecto:
 *   apellido1 + apellido2 + nom + f_nacimiento
 * (normalizado: lower/trim; apellido2 NULL ≡ '').
 *
 * Uso:
 *   php tools/audit/audit_personas_paso_duplicadas.php
 *   php tools/audit/audit_personas_paso_duplicadas.php --database=sv
 *   php tools/audit/audit_personas_paso_duplicadas.php --fase=lista
 *   php tools/audit/audit_personas_paso_duplicadas.php --fase=relacionados
 *   php tools/audit/audit_personas_paso_duplicadas.php --fase=ambas
 *   php tools/audit/audit_personas_paso_duplicadas.php --match=nombres
 *   php tools/audit/audit_personas_paso_duplicadas.php --schema=restov
 *   php tools/audit/audit_personas_paso_duplicadas.php --json
 *   php tools/audit/audit_personas_paso_duplicadas.php --limit=50
 *
 * --match=fecha   (default) exige f_nacimiento no nula e igual en ambos lados
 * --match=nombres solo apellido1+apellido2+nom (más permisivo; revisar a mano)
 *
 * @see restov.p_de_paso_ex / publicv.p_de_paso / global.personas
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

require dirname(__DIR__, 2) . '/src/shared/global_header.inc';

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

$database = 'sv';
$fase = 'ambas'; // lista | relacionados | ambas
$match = 'fecha'; // fecha | nombres
$schemaFilter = null;
$jsonOutput = false;
$limit = 0;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--database=')) {
        $database = substr($arg, strlen('--database='));
    }
    if (str_starts_with($arg, '--fase=')) {
        $fase = substr($arg, strlen('--fase='));
    }
    if (str_starts_with($arg, '--match=')) {
        $match = substr($arg, strlen('--match='));
    }
    if (str_starts_with($arg, '--schema=')) {
        $schemaFilter = substr($arg, strlen('--schema='));
    }
    if (str_starts_with($arg, '--limit=')) {
        $limit = (int) substr($arg, strlen('--limit='));
    }
    if ($arg === '--json') {
        $jsonOutput = true;
    }
}

if (!in_array($fase, ['lista', 'relacionados', 'ambas'], true)) {
    fwrite(STDERR, "fase inválida: use lista|relacionados|ambas\n");
    exit(1);
}
if (!in_array($match, ['fecha', 'nombres'], true)) {
    fwrite(STDERR, "match inválido: use fecha|nombres\n");
    exit(1);
}

ConfigGlobal::setTest_mode(true);
putenv('UBICACION=' . ($database === 'sf' ? 'sf' : 'sv'));

/**
 * @return list<string>
 */
function quoteIdent(string $ident): string
{
    return '"' . str_replace('"', '""', $ident) . '"';
}

/**
 * Condición JOIN paso ↔ global.personas.
 */
function matchJoinSql(string $match): string
{
    $base = <<<'SQL'
  lower(trim(p.apellido1)) = lower(trim(g.apellido1))
  AND lower(trim(coalesce(p.apellido2, ''))) = lower(trim(coalesce(g.apellido2, '')))
  AND lower(trim(p.nom)) = lower(trim(g.nom))
SQL;
    if ($match === 'fecha') {
        return $base . "\n  AND p.f_nacimiento IS NOT NULL\n  AND g.f_nacimiento IS NOT NULL\n  AND p.f_nacimiento = g.f_nacimiento";
    }

    return $base;
}

/**
 * Tablas a inventariar por id_nom / id_pau, si existen en el esquema (o vistas públicas).
 *
 * @return list<array{tabla: string, columna: string, scope: 'mismo_esquema'|'public_suffix'}>
 */
function tablasRelacionadas(): array
{
    return [
        ['tabla' => 'd_asistentes_ex', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        ['tabla' => 'd_teleco_personas_ex', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        ['tabla' => 'd_cargos_activ_dl', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        ['tabla' => 'd_dossiers_abiertos', 'columna' => 'id_pau', 'scope' => 'mismo_esquema'],
        ['tabla' => 'd_ultima_asistencia', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        ['tabla' => 'd_traslados', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        ['tabla' => 'e_notas', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        ['tabla' => 'e_notas_dl', 'columna' => 'id_nom', 'scope' => 'mismo_esquema'],
        // Vista/unión pública de asistentes de paso
        ['tabla' => 'd_asistentes_de_paso', 'columna' => 'id_nom', 'scope' => 'public_suffix'],
    ];
}

try {
    $configDB = new ConfigDB($database);
    // En CLI test_mode mi_sfsv() puede ser 0; el esquema público sigue a --database.
    $publicSchema = $database === 'sf' ? 'publicf' : 'publicv';
    $config = $configDB->getEsquema($publicSchema);
    $pdo = (new DBConnection($config))->getPDO();
    $pdo->exec("SET statement_timeout = '300s'");

    $stmtSchemas = $pdo->query(
        "SELECT n.nspname AS schema
         FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
         WHERE c.relname = 'p_de_paso_ex'
           AND n.nspname NOT LIKE 'pg_%'
           AND n.nspname NOT LIKE 'information_schema%'
         ORDER BY n.nspname"
    );
    if ($stmtSchemas === false) {
        throw new RuntimeException('No se pudieron listar esquemas con p_de_paso_ex.');
    }
    /** @var list<string> $schemas */
    $schemas = $stmtSchemas->fetchAll(PDO::FETCH_COLUMN) ?: [];
    if ($schemaFilter !== null) {
        $schemas = array_values(array_filter($schemas, static fn(string $s): bool => $s === $schemaFilter));
    }

    $joinCond = matchJoinSql($match);
    $limitSql = $limit > 0 ? ' LIMIT ' . $limit : '';

    /** @var list<array<string, mixed>> $duplicados */
    $duplicados = [];
    /** @var list<int> $idsPaso */
    $idsPaso = [];

    if ($fase === 'lista' || $fase === 'ambas') {
        foreach ($schemas as $schema) {
            $q = quoteIdent($schema);
            $schemaLit = $pdo->quote($schema);
            $sql = <<<SQL
SELECT
  {$schemaLit} AS schema_paso,
  p.id_nom AS id_nom_paso,
  p.id_tabla AS id_tabla_paso,
  p.dl AS dl_paso,
  p.situacion AS situacion_paso,
  p.apellido1,
  p.apellido2,
  p.nom,
  p.f_nacimiento::text AS f_nacimiento,
  g.id_nom AS id_nom_orbix,
  g.dl AS dl_orbix,
  g.id_tabla AS id_tabla_orbix,
  g.situacion AS situacion_orbix
FROM {$q}.p_de_paso_ex p
JOIN global.personas g ON
{$joinCond}
WHERE p.situacion = 'A'
  AND coalesce(trim(p.apellido1), '') <> ''
  AND coalesce(trim(p.nom), '') <> ''
ORDER BY p.apellido1, p.apellido2, p.nom, p.id_nom
{$limitSql}
SQL;
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($rows as $row) {
                $duplicados[] = $row;
                $idsPaso[] = (int) $row['id_nom_paso'];
            }
        }
        $idsPaso = array_values(array_unique($idsPaso));
    }

    // Si solo piden relacionados sin lista previa: tomar todos los id_nom activos de paso
    // (o los del filtro) — en práctica mejor pasar por lista.
    if ($fase === 'relacionados' && $idsPaso === []) {
        foreach ($schemas as $schema) {
            $q = quoteIdent($schema);
            $extra = $match === 'fecha' ? ' AND f_nacimiento IS NOT NULL' : '';
            $sql = "SELECT id_nom FROM {$q}.p_de_paso_ex
                    WHERE situacion = 'A'
                      AND coalesce(trim(apellido1), '') <> ''
                      AND coalesce(trim(nom), '') <> ''
                      {$extra}
                    ORDER BY id_nom
                    {$limitSql}";
            $ids = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN) ?: [];
            foreach ($ids as $id) {
                $idsPaso[] = (int) $id;
            }
        }
        $idsPaso = array_values(array_unique($idsPaso));
    }

    /** @var list<array<string, mixed>> $relacionados */
    $relacionados = [];

    if (($fase === 'relacionados' || $fase === 'ambas') && $idsPaso !== []) {
        // Mapa id_nom → schema de paso
        $idToSchema = [];
        foreach ($schemas as $schema) {
            $q = quoteIdent($schema);
            $stmt = $pdo->query("SELECT id_nom FROM {$q}.p_de_paso_ex");
            if ($stmt === false) {
                continue;
            }
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) ?: [] as $id) {
                $idToSchema[(int) $id] = $schema;
            }
        }

        $relDefs = tablasRelacionadas();
        // Resolver existencia de tablas públicas una vez
        $publicExists = [];
        foreach ($relDefs as $def) {
            if ($def['scope'] !== 'public_suffix') {
                continue;
            }
            $reg = $pdo->query(
                'SELECT to_regclass(' . $pdo->quote($publicSchema . '.' . $def['tabla']) . ')'
            )->fetchColumn();
            $publicExists[$def['tabla']] = $reg !== false && $reg !== null && $reg !== '';
        }

        foreach ($idsPaso as $idNom) {
            $schema = $idToSchema[$idNom] ?? null;
            if ($schema === null) {
                continue;
            }
            $q = quoteIdent($schema);
            $persona = $pdo->query(
                "SELECT id_nom, id_tabla, dl, apellido1, apellido2, nom, f_nacimiento::text, situacion
                 FROM {$q}.p_de_paso_ex WHERE id_nom = " . (int) $idNom
            )->fetch(PDO::FETCH_ASSOC);
            if ($persona === false) {
                continue;
            }

            $counts = [];
            $total = 0;
            foreach ($relDefs as $def) {
                $tabla = $def['tabla'];
                $col = $def['columna'];
                if ($def['scope'] === 'mismo_esquema') {
                    $reg = $pdo->query(
                        'SELECT to_regclass(' . $pdo->quote($schema . '.' . $tabla) . ')'
                    )->fetchColumn();
                    if ($reg === false || $reg === null || $reg === '') {
                        continue;
                    }
                    $from = $q . '.' . $tabla;
                } else {
                    if (!($publicExists[$tabla] ?? false)) {
                        continue;
                    }
                    $from = quoteIdent($publicSchema) . '.' . $tabla;
                }
                $n = (int) $pdo->query(
                    "SELECT count(*) FROM {$from} WHERE {$col} = " . (int) $idNom
                )->fetchColumn();
                if ($n > 0) {
                    $counts[$tabla] = $n;
                    $total += $n;
                }
            }

            $relacionados[] = [
                'schema_paso' => $schema,
                'id_nom_paso' => $idNom,
                'dl' => $persona['dl'],
                'apellido1' => $persona['apellido1'],
                'apellido2' => $persona['apellido2'],
                'nom' => $persona['nom'],
                'f_nacimiento' => $persona['f_nacimiento'],
                'total_relacionados' => $total,
                'detalle' => $counts,
                'seguro_borrar' => $total === 0,
            ];
        }

        usort(
            $relacionados,
            static function (array $a, array $b): int {
                return [$b['total_relacionados'], $a['apellido1'] ?? '', $a['nom'] ?? '']
                    <=> [$a['total_relacionados'], $b['apellido1'] ?? '', $b['nom'] ?? ''];
            }
        );
    }

    $report = [
        'database' => $database,
        'public_schema' => $publicSchema,
        'match' => $match,
        'fase' => $fase,
        'schemas_p_de_paso_ex' => $schemas,
        'duplicados' => $duplicados,
        'duplicados_paso_unicos' => count(array_unique(array_column($duplicados, 'id_nom_paso'))),
        'duplicados_filas' => count($duplicados),
        'relacionados' => $relacionados,
        'relacionados_con_datos' => count(array_filter(
            $relacionados,
            static fn(array $r): bool => ($r['total_relacionados'] ?? 0) > 0
        )),
        'relacionados_seguros' => count(array_filter(
            $relacionados,
            static fn(array $r): bool => !empty($r['seguro_borrar'])
        )),
    ];
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: requiere BD PostgreSQL accesible vía ConfigDB ({$database}).\n");
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

if ($jsonOutput) {
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

echo "Auditoría personas de paso duplicadas — database={$database} match={$match} fase={$fase}\n";
echo 'Esquemas con p_de_paso_ex: ' . (empty($schemas) ? '(ninguno)' : implode(', ', $schemas)) . "\n\n";

if ($fase === 'lista' || $fase === 'ambas') {
    echo "=== 1) Lista de duplicados (paso ↔ global.personas) ===\n";
    echo "Filas match: {$report['duplicados_filas']} | Personas de paso únicas: {$report['duplicados_paso_unicos']}\n";
    if ($duplicados === []) {
        echo "(ningún duplicado con el criterio actual)\n";
        if ($match === 'fecha') {
            echo "Hint: muchas de paso no tienen f_nacimiento; prueba --match=nombres (revisar a mano).\n";
        }
    } else {
        foreach ($duplicados as $row) {
            echo sprintf(
                "  paso id=%s [%s/%s] ↔ orbix id=%s [%s/%s] | %s %s, %s | nac=%s\n",
                $row['id_nom_paso'],
                $row['schema_paso'],
                $row['dl_paso'] ?? '',
                $row['id_nom_orbix'],
                $row['dl_orbix'] ?? '',
                $row['id_tabla_orbix'] ?? '',
                $row['apellido1'] ?? '',
                $row['apellido2'] ?? '',
                $row['nom'] ?? '',
                $row['f_nacimiento'] ?? ''
            );
        }
    }
    echo "\n";
}

if ($fase === 'relacionados' || $fase === 'ambas') {
    echo "=== 2) Datos asociados (antes de eliminar) ===\n";
    if ($fase === 'ambas' && $duplicados === []) {
        echo "(sin candidatos de la lista; no se inventarían todos los de paso)\n";
    } elseif ($relacionados === []) {
        echo "(ningún id a inventariar)\n";
    } else {
        echo "Candidatos: " . count($relacionados)
            . " | con datos: {$report['relacionados_con_datos']}"
            . " | sin datos (seguros): {$report['relacionados_seguros']}\n";
        foreach ($relacionados as $row) {
            $parts = [];
            foreach ($row['detalle'] as $tabla => $n) {
                $parts[] = "{$tabla}={$n}";
            }
            $det = $parts === [] ? '—' : implode(', ', $parts);
            echo sprintf(
                "  id=%s [%s] %s %s, %s | total=%d | %s\n",
                $row['id_nom_paso'],
                $row['schema_paso'],
                $row['apellido1'] ?? '',
                $row['apellido2'] ?? '',
                $row['nom'] ?? '',
                $row['total_relacionados'],
                $det
            );
        }
    }
}

exit(0);
