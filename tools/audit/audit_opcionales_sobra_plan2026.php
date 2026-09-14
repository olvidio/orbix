#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Auditoría (solo lectura): opcionales «de sobra» en alumnos plan 2026 y huecos libres 2430–2434.
 *
 * Criterio alineado con NotasDeUnaPersonaData y la migración
 * 202609141200_e_notas_opcional_sobra_plan2026__sv.sql / __sf.sql.
 *
 * Uso:
 *   php tools/audit/audit_opcionales_sobra_plan2026.php
 *   php tools/audit/audit_opcionales_sobra_plan2026.php --database=sf
 *   php tools/audit/audit_opcionales_sobra_plan2026.php --json
 *
 * Aplicar corrección: devel_db_admin → Migraciones →
 *   202609141200_e_notas_opcional_sobra_plan2026__sv.sql (o __sf).
 *
 * @see docs/manual/CambiosStgr2026.md
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

$sqlCandidatas = <<<SQL
SELECT n.id_nom, n.id_asignatura, n.id_nivel, n.tipo_acta, n.acta, n.nota
FROM {$publicSchema}.e_notas AS n
WHERE (
    n.id_nivel IN (1230, 1231, 1232)
    OR (n.id_asignatura > 3000 AND NOT (n.id_nivel BETWEEN 2430 AND 2434))
)
  AND NOT EXISTS (
      SELECT 1
      FROM {$publicSchema}.e_notas AS fin
      WHERE fin.id_nom = n.id_nom
        AND fin.id_asignatura = 9998
        AND fin.f_acta IS NOT NULL
        AND fin.f_acta < DATE '2026-09-30'
  )
ORDER BY n.id_nom, n.id_nivel, n.id_asignatura
SQL;

$stmt = $pdo->query($sqlCandidatas);
$filas = $stmt !== false ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$porNom = [];
foreach ($filas as $fila) {
    $idNom = (int) $fila['id_nom'];
    $porNom[$idNom][] = $fila;
}

$reubicables = 0;
$sinHueco = 0;
$detalle = [];

foreach ($porNom as $idNom => $notas) {
    $ocupados = [];
    $stmtOcc = $pdo->prepare(
        "SELECT id_nivel FROM {$publicSchema}.e_notas WHERE id_nom = :id_nom AND id_nivel BETWEEN 2430 AND 2434"
    );
    $stmtOcc->execute(['id_nom' => $idNom]);
    foreach ($stmtOcc->fetchAll(PDO::FETCH_COLUMN) as $nivel) {
        $ocupados[(int) $nivel] = true;
    }

    foreach ($notas as $nota) {
        $idNivel = (int) $nota['id_nivel'];
        if ($idNivel >= 2430 && $idNivel <= 2434) {
            continue;
        }

        $cand = null;
        for ($i = 2430; $i <= 2434; $i++) {
            if (!isset($ocupados[$i])) {
                $cand = $i;
                $ocupados[$i] = true;
                break;
            }
        }

        $item = [
            'id_nom' => $idNom,
            'id_asignatura' => (int) $nota['id_asignatura'],
            'id_nivel_actual' => $idNivel,
            'tipo_acta' => $nota['tipo_acta'],
            'acta' => $nota['acta'],
            'nota' => $nota['nota'],
            'id_nivel_destino' => $cand,
        ];
        $detalle[] = $item;

        if ($cand === null) {
            $sinHueco++;
        } else {
            $reubicables++;
        }
    }
}

$resumen = [
    'database' => $database,
    'schema' => $publicSchema,
    'candidatas_total' => count($filas),
    'alumnos_afectados' => count($porNom),
    'reubicables' => $reubicables,
    'sin_hueco' => $sinHueco,
    'detalle' => $detalle,
];

if ($jsonOutput) {
    echo json_encode($resumen, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

echo "Opcionales de sobra — plan 2026 ({$database}, {$publicSchema})\n";
echo str_repeat('-', 60) . "\n";
echo "Notas candidatas:     {$resumen['candidatas_total']}\n";
echo "Alumnos afectados:    {$resumen['alumnos_afectados']}\n";
echo "Reubicables (hay hueco): {$resumen['reubicables']}\n";
echo "Sin hueco 2430–2434:  {$resumen['sin_hueco']}\n";

if ($detalle !== []) {
    echo "\nDetalle (id_nom, id_asignatura, id_nivel → destino):\n";
    foreach ($detalle as $row) {
        $dest = $row['id_nivel_destino'] === null ? '—' : (string) $row['id_nivel_destino'];
        echo sprintf(
            "  %d  asig=%d  nivel=%d → %s  (acta=%s)\n",
            $row['id_nom'],
            $row['id_asignatura'],
            $row['id_nivel_actual'],
            $dest,
            (string) $row['acta']
        );
    }
}

echo "\nAplicar: devel_db_admin → Migraciones → 202609141200_e_notas_opcional_sobra_plan2026__{$database}.sql\n";
