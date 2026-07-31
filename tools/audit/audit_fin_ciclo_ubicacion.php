#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Diagnóstico (solo lectura) de marcas fin de ciclo 9998/9999 mal ubicadas.
 *
 * Criterio de destino (igual que la migración 271800 / ActaFinCicloInsert):
 *   - esquema de la última acta tipo 1 en e_notas_dl
 *     (9999: id_nivel < 2000; 9998: cualquier nivel salvo 9998/9999)
 *   - fallback: prefijo del campo `acta` vía log/db/mapa_prefijo_acta_esquema.csv
 *
 * Uso:
 *   php tools/audit/audit_fin_ciclo_ubicacion.php
 *   php tools/audit/audit_fin_ciclo_ubicacion.php --database=sf
 *   php tools/audit/audit_fin_ciclo_ubicacion.php --json
 *
 * @see db/migrations/202607271800_mover_fin_ciclo_a_dl_ultima_acta__sv.sql
 * @see docs/dev/notas_modelo_acta.md
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
require $root . '/src/shared/global_header.inc';

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

$database = 'sv';
$jsonOutput = false;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--database=')) {
        $database = substr($arg, strlen('--database='));
    }
    if ($arg === '--json') {
        $jsonOutput = true;
    }
}

ConfigGlobal::setTest_mode(true);
putenv('UBICACION=' . ($database === 'sf' ? 'sf' : 'sv'));

$configDB = new ConfigDB($database);
$suffix = ConfigGlobal::mi_sfsv() === 1 ? 'v' : 'f';
$publicSchema = 'public' . $suffix;
$pdo = (new DBConnection($configDB->getEsquema($publicSchema)))->getPDO();

$csvPath = $root . '/log/db/mapa_prefijo_acta_esquema.csv';
if (!is_readable($csvPath)) {
    fwrite(STDERR, "No se puede leer {$csvPath} (exportar con 211110 comun).\n");
    exit(1);
}

$pdo->exec('CREATE TEMP TABLE tmp_mapa_fin (pref text PRIMARY KEY, esquema_base text NOT NULL)');
$ins = $pdo->prepare('INSERT INTO tmp_mapa_fin (pref, esquema_base) VALUES (:p, :b)');
$fh = fopen($csvPath, 'r');
if ($fh === false) {
    fwrite(STDERR, "No se pudo abrir el CSV del mapa.\n");
    exit(1);
}
while (($row = fgetcsv($fh)) !== false) {
    if (!isset($row[0], $row[1]) || $row[0] === '' || $row[0] === 'pref') {
        continue;
    }
    $ins->execute(['p' => (string) $row[0], 'b' => (string) $row[1]]);
}
fclose($fh);

$pdo->exec(
    'CREATE TEMP TABLE tmp_fin_ciclo (
        esquema text NOT NULL,
        tabla text NOT NULL,
        id_nom integer NOT NULL,
        id_asignatura integer NOT NULL,
        acta text
    )'
);

$tablesStmt = $pdo->query(
    "SELECT n.nspname AS schema, c.relname AS tabla
     FROM pg_class c
     JOIN pg_namespace n ON n.oid = c.relnamespace
     WHERE c.relname IN ('e_notas_dl', 'e_notas_otra_region_stgr')
       AND n.nspname NOT LIKE 'pg_%'
       AND n.nspname <> 'information_schema'
     ORDER BY 1, 2"
);
if ($tablesStmt === false) {
    fwrite(STDERR, "No se pudieron listar tablas de notas.\n");
    exit(1);
}

foreach ($tablesStmt->fetchAll(PDO::FETCH_ASSOC) as $t) {
    $schema = (string) $t['schema'];
    $tabla = (string) $t['tabla'];
    $qSchema = '"' . str_replace('"', '""', $schema) . '"';
    $qTabla = '"' . str_replace('"', '""', $tabla) . '"';
    $pdo->exec(
        "INSERT INTO tmp_fin_ciclo (esquema, tabla, id_nom, id_asignatura, acta)
         SELECT {$pdo->quote($schema)}, {$pdo->quote($tabla)}, id_nom, id_asignatura, acta
         FROM {$qSchema}.{$qTabla}
         WHERE id_asignatura IN (9998, 9999)
           AND id_situacion IS DISTINCT FROM 13"
    );
}

$diagSql = <<<SQL
WITH ultima AS (
    SELECT DISTINCT ON (f.esquema, f.tabla, f.id_nom, f.id_asignatura)
           f.esquema, f.tabla, f.id_nom, f.id_asignatura, n.nspname AS esquema_ultima
    FROM tmp_fin_ciclo f
    JOIN {$publicSchema}.e_notas a ON a.id_nom = f.id_nom
    JOIN pg_class c ON c.oid = a.tableoid
    JOIN pg_namespace n ON n.oid = c.relnamespace
    WHERE a.id_asignatura NOT IN (9998, 9999)
      AND COALESCE(a.tipo_acta, 1) = 1
      AND a.f_acta IS NOT NULL
      AND c.relname = 'e_notas_dl'
      AND (f.id_asignatura = 9998 OR a.id_nivel < 2000)
    ORDER BY f.esquema, f.tabla, f.id_nom, f.id_asignatura, a.f_acta DESC NULLS LAST
),
resuelto AS (
    SELECT f.esquema, f.tabla,
           COALESCE(
               u.esquema_ultima,
               CASE WHEN m.esquema_base IS NOT NULL THEN m.esquema_base || '{$suffix}' ELSE NULL END
           ) AS dest
    FROM tmp_fin_ciclo f
    LEFT JOIN ultima u
      ON u.esquema = f.esquema AND u.tabla = f.tabla
     AND u.id_nom = f.id_nom AND u.id_asignatura = f.id_asignatura
    LEFT JOIN tmp_mapa_fin m
      ON m.pref = lower(trim(split_part(trim(coalesce(f.acta, '')), ' ', 1)))
     AND lower(trim(split_part(trim(coalesce(f.acta, '')), ' ', 1))) NOT LIKE 'fin%'
),
clasificado AS (
    SELECT r.*,
           CASE
               WHEN r.dest IS NULL THEN 'sin_destino'
               WHEN to_regclass(format('%I.e_notas_dl', r.dest)) IS NULL THEN 'destino_sin_dl'
               WHEN lower(r.esquema) = lower(r.dest) AND r.tabla = 'e_notas_dl' THEN 'ya_ok'
               ELSE 'a_mover'
           END AS estado
    FROM resuelto r
)
SELECT
    count(*) AS total,
    count(*) FILTER (WHERE estado = 'ya_ok') AS ya_ok,
    count(*) FILTER (WHERE estado = 'a_mover') AS a_mover,
    count(*) FILTER (WHERE estado = 'sin_destino') AS sin_destino,
    count(*) FILTER (WHERE estado = 'destino_sin_dl') AS destino_sin_dl
FROM clasificado
SQL;

$diag = $pdo->query($diagSql);
if ($diag === false) {
    fwrite(STDERR, "Error en consulta de diagnóstico.\n");
    exit(1);
}
/** @var array{total: int|string, ya_ok: int|string, a_mover: int|string, sin_destino: int|string, destino_sin_dl: int|string} $resumen */
$resumen = $diag->fetch(PDO::FETCH_ASSOC) ?: [
    'total' => 0,
    'ya_ok' => 0,
    'a_mover' => 0,
    'sin_destino' => 0,
    'destino_sin_dl' => 0,
];

$moves = $pdo->query(
    <<<SQL
    WITH ultima AS (
        SELECT DISTINCT ON (f.esquema, f.tabla, f.id_nom, f.id_asignatura)
               f.esquema, f.tabla, f.id_nom, f.id_asignatura, n.nspname AS esquema_ultima
        FROM tmp_fin_ciclo f
        JOIN {$publicSchema}.e_notas a ON a.id_nom = f.id_nom
        JOIN pg_class c ON c.oid = a.tableoid
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE a.id_asignatura NOT IN (9998, 9999)
          AND COALESCE(a.tipo_acta, 1) = 1
          AND a.f_acta IS NOT NULL
          AND c.relname = 'e_notas_dl'
          AND (f.id_asignatura = 9998 OR a.id_nivel < 2000)
        ORDER BY f.esquema, f.tabla, f.id_nom, f.id_asignatura, a.f_acta DESC NULLS LAST
    ),
    resuelto AS (
        SELECT f.esquema, f.tabla,
               COALESCE(
                   u.esquema_ultima,
                   CASE WHEN m.esquema_base IS NOT NULL THEN m.esquema_base || '{$suffix}' ELSE NULL END
               ) AS dest
        FROM tmp_fin_ciclo f
        LEFT JOIN ultima u
          ON u.esquema = f.esquema AND u.tabla = f.tabla
         AND u.id_nom = f.id_nom AND u.id_asignatura = f.id_asignatura
        LEFT JOIN tmp_mapa_fin m
          ON m.pref = lower(trim(split_part(trim(coalesce(f.acta, '')), ' ', 1)))
         AND lower(trim(split_part(trim(coalesce(f.acta, '')), ' ', 1))) NOT LIKE 'fin%'
    )
    SELECT esquema || '.' || tabla || ' → ' || dest || '.e_notas_dl' AS movimiento, count(*) AS n
    FROM resuelto
    WHERE dest IS NOT NULL
      AND to_regclass(format('%I.e_notas_dl', dest)) IS NOT NULL
      AND NOT (lower(esquema) = lower(dest) AND tabla = 'e_notas_dl')
    GROUP BY 1
    ORDER BY n DESC, 1
    SQL
);
$porOrigenDestino = [];
if ($moves !== false) {
    foreach ($moves->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $porOrigenDestino[(string) $row['movimiento']] = (int) $row['n'];
    }
}

$payload = [
    'database' => $database,
    'total' => (int) $resumen['total'],
    'ya_ok' => (int) $resumen['ya_ok'],
    'a_mover' => (int) $resumen['a_mover'],
    'sin_destino' => (int) $resumen['sin_destino'],
    'destino_sin_dl' => (int) $resumen['destino_sin_dl'],
    'por_origen_destino' => $porOrigenDestino,
];

if ($jsonOutput) {
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

echo "Fin ciclo 9998/9999 — ubicación ({$database})\n";
echo "  total:          {$payload['total']}\n";
echo "  ya_ok:          {$payload['ya_ok']}\n";
echo "  a_mover:        {$payload['a_mover']}\n";
echo "  sin_destino:    {$payload['sin_destino']}\n";
echo "  destino_sin_dl: {$payload['destino_sin_dl']}\n";
if ($porOrigenDestino !== []) {
    echo "  movimientos previstos:\n";
    foreach ($porOrigenDestino as $k => $n) {
        echo "    {$k}: {$n}\n";
    }
}
echo "\nMigración: db/migrations/202607271800_mover_fin_ciclo_a_dl_ultima_acta__{$database}.sql\n";
