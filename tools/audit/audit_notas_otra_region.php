#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Inventario de filas legacy en e_notas_otra_region_stgr y placeholders
 * FALTA_CERTIFICADO (id_situacion=13, tipo_acta=2) en e_notas_dl.
 *
 * Solo lectura. Requiere conexión PostgreSQL (ConfigDB / .env).
 *
 * Uso:
 *   php tools/audit/audit_notas_otra_region.php
 *   php tools/audit/audit_notas_otra_region.php --database=sv
 *   php tools/audit/audit_notas_otra_region.php --json
 *
 * @see docs/dev/notas_modelo_acta.md Slice 4
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

require dirname(__DIR__, 2) . '/src/shared/global_header.inc';

use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
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

/** @var list<array{schema: string, total: int, reales: int, certificados: int}> */
$otraRegion = [];

/** @var list<array{schema: string, placeholders: int}> */
$placeholdersDl = [];

try {
    $configDB = new ConfigDB($database);
    $suffix = ConfigGlobal::mi_sfsv() === 1 ? 'v' : 'f';
    $publicSchema = 'public' . $suffix;
    $config = $configDB->getEsquema($publicSchema);
    $pdo = (new DBConnection($config))->getPDO();

    $stmt = $pdo->query(
        "SELECT n.nspname AS schema
         FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
         WHERE c.relname = 'e_notas_otra_region_stgr'
           AND n.nspname NOT LIKE 'pg_%'
           AND n.nspname NOT LIKE 'information_schema%'
         ORDER BY n.nspname"
    );
    if ($stmt === false) {
        throw new RuntimeException('No se pudo listar esquemas con e_notas_otra_region_stgr.');
    }

    $schemasOtraRegion = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    foreach ($schemasOtraRegion as $schema) {
        $quoted = '"' . str_replace('"', '""', (string) $schema) . '"';
        $countStmt = $pdo->query("SELECT COUNT(*) FROM {$quoted}.e_notas_otra_region_stgr");
        $total = $countStmt !== false ? (int) $countStmt->fetchColumn() : 0;

        $realesStmt = $pdo->query(
            "SELECT COUNT(*) FROM {$quoted}.e_notas_otra_region_stgr
             WHERE COALESCE(tipo_acta, " . TipoActa::FORMATO_ACTA . ") = " . TipoActa::FORMATO_ACTA . "
               AND id_situacion <> " . NotaSituacion::FALTA_CERTIFICADO
        );
        $reales = $realesStmt !== false ? (int) $realesStmt->fetchColumn() : 0;

        $certStmt = $pdo->query(
            "SELECT COUNT(*) FROM {$quoted}.e_notas_otra_region_stgr
             WHERE COALESCE(tipo_acta, " . TipoActa::FORMATO_ACTA . ") = " . TipoActa::FORMATO_CERTIFICADO
        );
        $certificados = $certStmt !== false ? (int) $certStmt->fetchColumn() : 0;

        $otraRegion[] = [
            'schema' => (string) $schema,
            'total' => $total,
            'reales' => $reales,
            'certificados' => $certificados,
        ];
    }

    $stmtDl = $pdo->query(
        "SELECT n.nspname AS schema
         FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
         WHERE c.relname = 'e_notas_dl'
           AND n.nspname NOT LIKE 'pg_%'
           AND n.nspname NOT LIKE 'information_schema%'
         ORDER BY n.nspname"
    );
    if ($stmtDl === false) {
        throw new RuntimeException('No se pudo listar esquemas con e_notas_dl.');
    }

    $schemasDl = $stmtDl->fetchAll(PDO::FETCH_COLUMN) ?: [];

    foreach ($schemasDl as $schema) {
        $quoted = '"' . str_replace('"', '""', (string) $schema) . '"';
        $phStmt = $pdo->query(
            "SELECT COUNT(*) FROM {$quoted}.e_notas_dl
             WHERE id_situacion = " . NotaSituacion::FALTA_CERTIFICADO . "
               AND COALESCE(tipo_acta, " . TipoActa::FORMATO_ACTA . ") = " . TipoActa::FORMATO_CERTIFICADO
        );
        $placeholders = $phStmt !== false ? (int) $phStmt->fetchColumn() : 0;
        if ($placeholders > 0) {
            $placeholdersDl[] = [
                'schema' => (string) $schema,
                'placeholders' => $placeholders,
            ];
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: requiere BD PostgreSQL accesible vía ConfigDB ({$database}).\n");
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$report = [
    'database' => $database,
    'e_notas_otra_region_stgr' => $otraRegion,
    'e_notas_dl_placeholders_falta_certificado' => $placeholdersDl,
    'totals' => [
        'otra_region_filas' => array_sum(array_column($otraRegion, 'total')),
        'otra_region_reales' => array_sum(array_column($otraRegion, 'reales')),
        'otra_region_certificados' => array_sum(array_column($otraRegion, 'certificados')),
        'placeholders_dl' => array_sum(array_column($placeholdersDl, 'placeholders')),
    ],
];

if ($jsonOutput) {
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

echo "Auditoría notas legacy (modelo acta) — database={$database}\n\n";

echo "e_notas_otra_region_stgr por esquema:\n";
if ($otraRegion === []) {
    echo "  (ningún esquema con la tabla)\n";
} else {
    foreach ($otraRegion as $row) {
        echo sprintf(
            "  %s: total=%d, reales=%d, tipo_acta=2=%d\n",
            $row['schema'],
            $row['total'],
            $row['reales'],
            $row['certificados']
        );
    }
}

echo "\nPlaceholders FALTA_CERTIFICADO en e_notas_dl (tipo_acta=2, id_situacion=13):\n";
if ($placeholdersDl === []) {
    echo "  (ninguno)\n";
} else {
    foreach ($placeholdersDl as $row) {
        echo sprintf("  %s: %d filas\n", $row['schema'], $row['placeholders']);
    }
}

echo "\nTotales:\n";
echo "  otra_region filas: {$report['totals']['otra_region_filas']}\n";
echo "  otra_region notas reales (candidatas repatriación): {$report['totals']['otra_region_reales']}\n";
echo "  otra_region tipo_acta=2: {$report['totals']['otra_region_certificados']}\n";
echo "  placeholders e_notas_dl: {$report['totals']['placeholders_dl']}\n";

exit(0);
