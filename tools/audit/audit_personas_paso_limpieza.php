#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Inventario para reducir personas de paso (solo lectura).
 *
 * Buckets:
 *   A protegido_curso — asistencia a actividad con f_ini >= curso_ini (no tocar)
 *   B con_notas       — tiene notas en publicv.e_notas
 *   C solo_historial  — asistencias solo a actividades anteriores al curso
 *   D vacio_borrable  — sin notas ni asistencias
 *
 * También: duplicados internos (ape1+ape2+nom+dl) y cruce con global.personas.
 * Canónico: más notas → más asis → id_nom más reciente = más negativo (MIN).
 *
 * Producción (usuario SO postgres, sin ConfigDB / .inc):
 *   sudo -u postgres php tools/audit/audit_personas_paso_limpieza.php --pg --fase=resumen
 *   sudo -u postgres php tools/audit/audit_personas_paso_limpieza.php --pg --database=sv
 *   sudo -u postgres php tools/audit/audit_personas_paso_limpieza.php --pg --db-sv=sv --db-comun=comun
 *   # Opcional: PGHOST / PGPORT / PGUSER (libpq). Sin host → peer/socket local.
 *
 * Desarrollo (ConfigDB del repo):
 *   php tools/audit/audit_personas_paso_limpieza.php --fase=resumen
 *
 * Otras opciones:
 *   --curso-ini=2025-10-01
 *   --fase=resumen|buckets|duplicados|vs-global|vacios|todo
 *   --schema-paso=restov
 *   --json  --limit=30
 *
 * SQL de referencia: tools/audit/sql/personas_paso_limpieza.sql
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$database = 'sv';
$dbSvName = null;
$dbComunName = 'comun';
$cursoIni = '2025-10-01';
$fase = 'resumen'; // resumen|buckets|duplicados|vs-global|vacios|todo
$jsonOutput = false;
$limit = 0;
$pasoSchema = null; // restov / restof por defecto según database
$usePg = false; // conexión directa PDO (prod como postgres)

foreach ($argv as $arg) {
    if ($arg === '--pg') {
        $usePg = true;
    }
    if (str_starts_with($arg, '--database=')) {
        $database = substr($arg, strlen('--database='));
    }
    if (str_starts_with($arg, '--db-sv=')) {
        $dbSvName = substr($arg, strlen('--db-sv='));
    }
    if (str_starts_with($arg, '--db-comun=')) {
        $dbComunName = substr($arg, strlen('--db-comun='));
    }
    if (str_starts_with($arg, '--curso-ini=')) {
        $cursoIni = substr($arg, strlen('--curso-ini='));
    }
    if (str_starts_with($arg, '--fase=')) {
        $fase = substr($arg, strlen('--fase='));
    }
    if (str_starts_with($arg, '--schema-paso=')) {
        $pasoSchema = substr($arg, strlen('--schema-paso='));
    }
    if (str_starts_with($arg, '--limit=')) {
        $limit = (int) substr($arg, strlen('--limit='));
    }
    if ($arg === '--json') {
        $jsonOutput = true;
    }
}

$fasesOk = ['resumen', 'buckets', 'duplicados', 'vs-global', 'vacios', 'todo'];
if (!in_array($fase, $fasesOk, true)) {
    fwrite(STDERR, 'fase inválida: ' . implode('|', $fasesOk) . "\n");
    exit(1);
}

$dbSvName ??= $database;
$publicSchema = $database === 'sf' ? 'publicf' : 'publicv';
$pasoSchema ??= ($database === 'sf' ? 'restof' : 'restov');

/**
 * PDO pgsql vía libpq (peer/socket si no hay PGHOST). No usa ConfigDB.
 */
function pdoPg(string $dbname): PDO
{
    $parts = ['dbname=' . $dbname];
    $host = getenv('PGHOST');
    if (is_string($host) && $host !== '') {
        $parts[] = 'host=' . $host;
    }
    $port = getenv('PGPORT');
    if (is_string($port) && $port !== '') {
        $parts[] = 'port=' . $port;
    }
    $user = getenv('PGUSER');
    if (is_string($user) && $user !== '') {
        $parts[] = 'user=' . $user;
    }
    // Sin password: peer/trust como usuario SO (p.ej. postgres).
    $dsn = 'pgsql:' . implode(';', $parts);
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}

function qIdent(string $s): string
{
    return '"' . str_replace('"', '""', $s) . '"';
}

function normKey(?string $s): string
{
    return mb_strtolower(trim((string) $s), 'UTF-8');
}

/**
 * @param list<array<string, mixed>> $rows
 * @return list<array<string, mixed>>
 */
function applyLimit(array $rows, int $limit): array
{
    if ($limit <= 0) {
        return $rows;
    }

    return array_slice($rows, 0, $limit);
}

try {
    if ($usePg) {
        $pdoSv = pdoPg($dbSvName);
        $pdoComun = pdoPg($dbComunName);
        $connMode = 'pg';
    } else {
        require dirname(__DIR__, 2) . '/src/shared/global_header.inc';
        \src\shared\config\ConfigGlobal::setTest_mode(true);
        putenv('UBICACION=' . ($database === 'sf' ? 'sf' : 'sv'));
        $pdoSv = (new \src\shared\infrastructure\persistence\DBConnection(
            (new \src\shared\infrastructure\persistence\ConfigDB($database))->getEsquema($publicSchema)
        ))->getPDO();
        $pdoComun = (new \src\shared\infrastructure\persistence\DBConnection(
            (new \src\shared\infrastructure\persistence\ConfigDB('comun'))->getEsquema('public')
        ))->getPDO();
        $connMode = 'configdb';
        $dbSvName = $database;
        $dbComunName = 'comun';
    }
    $pdoSv->exec("SET statement_timeout = '300s'");
    $pdoComun->exec("SET statement_timeout = '120s'");

    $qPaso = qIdent($pasoSchema);
    $qPublic = qIdent($publicSchema);

    if ($pdoSv->query('SELECT to_regclass(' . $pdoSv->quote("{$pasoSchema}.p_de_paso_ex") . ')')->fetchColumn() === null) {
        throw new RuntimeException("No existe {$pasoSchema}.p_de_paso_ex");
    }

    // --- Personas de paso activas ---
    $pasoRows = $pdoSv->query(
        "SELECT id_nom, id_tabla, dl, apellido1, apellido2, nom, f_nacimiento::text AS f_nacimiento
         FROM {$qPaso}.p_de_paso_ex
         WHERE situacion = 'A'"
    )->fetchAll(PDO::FETCH_ASSOC) ?: [];

    /** @var array<int, array<string, mixed>> $pasoById */
    $pasoById = [];
    foreach ($pasoRows as $r) {
        $id = (int) $r['id_nom'];
        $pasoById[$id] = $r + [
            'k_ape1' => normKey($r['apellido1'] ?? ''),
            'k_ape2' => normKey($r['apellido2'] ?? ''),
            'k_nom' => normKey($r['nom'] ?? ''),
            'k_dl' => normKey($r['dl'] ?? ''),
            'n_notas' => 0,
            'n_asis' => 0,
            'id_activs' => [],
            'tiene_curso' => false,
            'bucket' => 'D',
        ];
    }
    $idsPaso = array_keys($pasoById);

    // --- Notas (public*.e_notas) ---
    if ($idsPaso !== []
        && $pdoSv->query('SELECT to_regclass(' . $pdoSv->quote("{$publicSchema}.e_notas") . ')')->fetchColumn()
    ) {
        $stmt = $pdoSv->query(
            "SELECT id_nom, count(*) AS n
             FROM {$qPublic}.e_notas
             WHERE id_nom IN (SELECT id_nom FROM {$qPaso}.p_de_paso_ex WHERE situacion = 'A')
             GROUP BY id_nom"
        );
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $r) {
            $id = (int) $r['id_nom'];
            if (isset($pasoById[$id])) {
                $pasoById[$id]['n_notas'] = (int) $r['n'];
            }
        }
    }

    // --- Asistencias ---
    /** @var array<int, list<int>> $activsByNom */
    $activsByNom = [];
    $asisTables = [];
    if ($pdoSv->query('SELECT to_regclass(' . $pdoSv->quote("{$publicSchema}.d_asistentes_de_paso") . ')')->fetchColumn()) {
        $asisTables[] = "{$qPublic}.d_asistentes_de_paso";
    }
    if ($pdoSv->query('SELECT to_regclass(' . $pdoSv->quote("{$pasoSchema}.d_asistentes_ex") . ')')->fetchColumn()) {
        $asisTables[] = "{$qPaso}.d_asistentes_ex";
    }
    foreach ($asisTables as $from) {
        $stmt = $pdoSv->query(
            "SELECT id_nom, id_activ FROM {$from}
             WHERE id_nom IN (SELECT id_nom FROM {$qPaso}.p_de_paso_ex WHERE situacion = 'A')"
        );
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $r) {
            $id = (int) $r['id_nom'];
            $idActiv = (int) $r['id_activ'];
            if (!isset($pasoById[$id])) {
                continue;
            }
            $activsByNom[$id][] = $idActiv;
            $pasoById[$id]['n_asis']++;
        }
    }
    foreach ($activsByNom as $id => $acts) {
        $pasoById[$id]['id_activs'] = array_values(array_unique($acts));
    }

    // --- Actividades del curso (comun) ---
    /** @var array<int, true> $activCurso */
    $activCurso = [];
    $allActivIds = [];
    foreach ($activsByNom as $acts) {
        foreach ($acts as $a) {
            $allActivIds[$a] = true;
        }
    }
    if ($allActivIds !== []) {
        // Cargar f_ini de las actividades referenciadas (y marcar curso)
        $ids = array_keys($allActivIds);
        foreach (array_chunk($ids, 500) as $chunk) {
            $in = implode(',', array_map('intval', $chunk));
            $stmt = $pdoComun->query(
                "SELECT id_activ, f_ini::text
                 FROM public.a_actividades_all
                 WHERE id_activ IN ({$in})"
            );
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $r) {
                $idActiv = (int) $r['id_activ'];
                $fIni = (string) ($r['f_ini'] ?? '');
                if ($fIni !== '' && $fIni >= $cursoIni) {
                    $activCurso[$idActiv] = true;
                }
            }
        }
        foreach ($pasoById as $id => &$row) {
            foreach ($row['id_activs'] as $idActiv) {
                if (isset($activCurso[$idActiv])) {
                    $row['tiene_curso'] = true;
                    break;
                }
            }
        }
        unset($row);
    }

    // --- Buckets ---
    $buckets = ['A' => [], 'B' => [], 'C' => [], 'D' => []];
    foreach ($pasoById as $id => &$row) {
        if ($row['tiene_curso']) {
            $row['bucket'] = 'A';
        } elseif ($row['n_notas'] > 0) {
            $row['bucket'] = 'B';
        } elseif ($row['n_asis'] > 0) {
            $row['bucket'] = 'C';
        } else {
            $row['bucket'] = 'D';
        }
        $buckets[$row['bucket']][] = $row;
    }
    unset($row);

    $resumen = [
        'conn' => $connMode,
        'db_sv' => $dbSvName,
        'db_comun' => $dbComunName,
        'database' => $database,
        'public_schema' => $publicSchema,
        'paso_schema' => $pasoSchema,
        'curso_ini' => $cursoIni,
        'total_paso_A' => count($pasoById),
        'A_protegido_curso' => count($buckets['A']),
        'B_con_notas' => count($buckets['B']),
        'C_solo_historial' => count($buckets['C']),
        'D_vacio_borrable' => count($buckets['D']),
        'asis_tablas' => $asisTables,
        'actividades_curso_tocadas' => count($activCurso),
    ];

    // --- Duplicados internos ---
    /** @var array<string, list<array<string, mixed>>> $grupos */
    $grupos = [];
    foreach ($pasoById as $row) {
        if ($row['k_ape1'] === '' || $row['k_nom'] === '') {
            continue;
        }
        $key = $row['k_ape1'] . '|' . $row['k_ape2'] . '|' . $row['k_nom'] . '|' . $row['k_dl'];
        $grupos[$key][] = $row;
    }
    $duplicados = [];
    foreach ($grupos as $key => $members) {
        if (count($members) < 2) {
            continue;
        }
        usort(
            $members,
            static function (array $a, array $b): int {
                // más notas, más asis, id_nom ASC (más negativo = más reciente)
                return [$b['n_notas'], $b['n_asis'], $a['id_nom']]
                    <=> [$a['n_notas'], $a['n_asis'], $b['id_nom']];
            }
        );
        $canonico = $members[0];
        foreach ($members as $m) {
            $duplicados[] = [
                'clave' => $key,
                'id_canonico' => (int) $canonico['id_nom'],
                'id_nom' => (int) $m['id_nom'],
                'es_canonico' => ((int) $m['id_nom'] === (int) $canonico['id_nom']),
                'dl' => $m['dl'],
                'apellido1' => $m['apellido1'],
                'apellido2' => $m['apellido2'],
                'nom' => $m['nom'],
                'f_nacimiento' => $m['f_nacimiento'],
                'n_notas' => $m['n_notas'],
                'n_asis' => $m['n_asis'],
                'bucket' => $m['bucket'],
                'n_grupo' => count($members),
            ];
        }
    }

    // --- vs global.personas (todos los de paso; el repaso filtrará canónicos) ---
    $vsGlobal = [];
    $stmt = $pdoSv->query(
        "SELECT id_nom, dl, id_tabla, apellido1, apellido2, nom, f_nacimiento::text AS f_nacimiento, situacion
         FROM global.personas"
    );
    /** @var array<string, list<array<string, mixed>>> $globalByKey */
    $globalByKey = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $g) {
        $k = normKey($g['apellido1'] ?? '') . '|' . normKey($g['apellido2'] ?? '')
            . '|' . normKey($g['nom'] ?? '');
        if (str_starts_with($k, '||') || str_ends_with($k, '||') || str_contains($k, '|||')) {
            // vacíos raros
        }
        if (normKey($g['apellido1'] ?? '') === '' || normKey($g['nom'] ?? '') === '') {
            continue;
        }
        $globalByKey[$k][] = $g;
    }
    foreach ($pasoById as $p) {
        if ($p['k_ape1'] === '' || $p['k_nom'] === '') {
            continue;
        }
        $k = $p['k_ape1'] . '|' . $p['k_ape2'] . '|' . $p['k_nom'];
        if (!isset($globalByKey[$k])) {
            continue;
        }
        foreach ($globalByKey[$k] as $g) {
            $vsGlobal[] = [
                'id_paso' => (int) $p['id_nom'],
                'dl_paso' => $p['dl'],
                'bucket' => $p['bucket'],
                'id_orbix' => (int) $g['id_nom'],
                'dl_orbix' => $g['dl'],
                'id_tabla_orbix' => $g['id_tabla'],
                'apellido1' => $p['apellido1'],
                'apellido2' => $p['apellido2'],
                'nom' => $p['nom'],
                'fn_paso' => $p['f_nacimiento'],
                'fn_orbix' => $g['f_nacimiento'],
                'misma_dl' => normKey($p['dl'] ?? '') === normKey($g['dl'] ?? ''),
                'misma_fn' => $p['f_nacimiento'] !== null && $p['f_nacimiento'] !== ''
                    && $g['f_nacimiento'] !== null && $g['f_nacimiento'] !== ''
                    && $p['f_nacimiento'] === $g['f_nacimiento'],
                'n_notas' => $p['n_notas'],
                'n_asis' => $p['n_asis'],
            ];
        }
    }
    usort(
        $vsGlobal,
        static function (array $a, array $b): int {
            return [$b['misma_dl'], $b['misma_fn'], $a['apellido1'], $a['nom'], $a['id_paso']]
                <=> [$a['misma_dl'], $a['misma_fn'], $b['apellido1'], $b['nom'], $b['id_paso']];
        }
    );

    $vacios = array_values(array_filter(
        $buckets['D'],
        static fn(array $r): bool => true
    ));
    usort(
        $vacios,
        static fn(array $a, array $b): int => [$a['apellido1'], $a['apellido2'], $a['nom'], $a['id_nom']]
            <=> [$b['apellido1'], $b['apellido2'], $b['nom'], $b['id_nom']]
    );

    $report = [
        'resumen' => $resumen,
        'buckets' => [
            'A' => applyLimit($buckets['A'], $limit),
            'B' => applyLimit($buckets['B'], $limit),
            'C' => applyLimit($buckets['C'], $limit),
            'D' => applyLimit($buckets['D'], $limit),
        ],
        'duplicados' => applyLimit($duplicados, $limit),
        'duplicados_grupos' => count(array_unique(array_column($duplicados, 'clave'))),
        'vs_global' => applyLimit($vsGlobal, $limit),
        'vs_global_filas' => count($vsGlobal),
        'vacios' => applyLimit($vacios, $limit),
    ];
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: BD vía ConfigDB (sv/comun).\n");
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

if ($jsonOutput) {
    $out = match ($fase) {
        'resumen' => $report['resumen'],
        'buckets' => ['resumen' => $report['resumen'], 'buckets' => $report['buckets']],
        'duplicados' => [
            'grupos' => $report['duplicados_grupos'],
            'filas' => $report['duplicados'],
        ],
        'vs-global' => [
            'filas' => $report['vs_global_filas'],
            'muestra' => $report['vs_global'],
        ],
        'vacios' => ['total' => $report['resumen']['D_vacio_borrable'], 'muestra' => $report['vacios']],
        default => $report,
    };
    echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

$r = $report['resumen'];
echo "Personas de paso — limpieza (solo lectura)\n";
echo "conn={$r['conn']} db_sv={$r['db_sv']} db_comun={$r['db_comun']} paso={$r['paso_schema']} curso_ini={$r['curso_ini']}\n\n";

if ($fase === 'resumen' || $fase === 'todo' || $fase === 'buckets') {
    echo "=== Resumen buckets ===\n";
    echo "  total situacion=A:     {$r['total_paso_A']}\n";
    echo "  A protegido_curso:     {$r['A_protegido_curso']}\n";
    echo "  B con_notas:           {$r['B_con_notas']}\n";
    echo "  C solo_historial:      {$r['C_solo_historial']}\n";
    echo "  D vacio_borrable:      {$r['D_vacio_borrable']}\n";
    echo '  tablas asis: ' . (empty($r['asis_tablas']) ? '(ninguna)' : implode(', ', $r['asis_tablas'])) . "\n\n";
}

if ($fase === 'buckets' || $fase === 'todo') {
    foreach (['A' => 'protegido_curso', 'B' => 'con_notas', 'C' => 'solo_historial', 'D' => 'vacio_borrable'] as $code => $label) {
        $rows = $report['buckets'][$code];
        echo "=== Bucket {$code} ({$label}) muestra ===\n";
        if ($rows === []) {
            echo "  (vacío)\n\n";
            continue;
        }
        foreach ($rows as $row) {
            echo sprintf(
                "  id=%s dl=%s | %s %s, %s | notas=%d asis=%d | nac=%s\n",
                $row['id_nom'],
                $row['dl'] ?? '',
                $row['apellido1'] ?? '',
                $row['apellido2'] ?? '',
                $row['nom'] ?? '',
                $row['n_notas'],
                $row['n_asis'],
                $row['f_nacimiento'] ?? ''
            );
        }
        echo "\n";
    }
}

if ($fase === 'duplicados' || $fase === 'todo') {
    echo "=== Duplicados internos (ape1+ape2+nom+dl) grupos={$report['duplicados_grupos']} ===\n";
    if ($report['duplicados'] === []) {
        echo "  (ninguno)\n\n";
    } else {
        foreach ($report['duplicados'] as $row) {
            echo sprintf(
                "  %s canónico=%s id=%s%s | %s %s, %s dl=%s | notas=%d asis=%d bucket=%s\n",
                $row['es_canonico'] ? '*' : ' ',
                $row['id_canonico'],
                $row['id_nom'],
                $row['es_canonico'] ? ' (canon)' : '',
                $row['apellido1'] ?? '',
                $row['apellido2'] ?? '',
                $row['nom'] ?? '',
                $row['dl'] ?? '',
                $row['n_notas'],
                $row['n_asis'],
                $row['bucket']
            );
        }
        echo "\n";
    }
}

if ($fase === 'vs-global' || $fase === 'todo') {
    echo "=== vs global.personas filas={$report['vs_global_filas']} ===\n";
    if ($report['vs_global'] === []) {
        echo "  (ninguno)\n\n";
    } else {
        foreach ($report['vs_global'] as $row) {
            echo sprintf(
                "  paso=%s[%s] ↔ orbix=%s[%s/%s] | %s %s, %s | misma_dl=%s misma_fn=%s | notas=%d asis=%d bucket=%s\n",
                $row['id_paso'],
                $row['dl_paso'] ?? '',
                $row['id_orbix'],
                $row['dl_orbix'] ?? '',
                $row['id_tabla_orbix'] ?? '',
                $row['apellido1'] ?? '',
                $row['apellido2'] ?? '',
                $row['nom'] ?? '',
                $row['misma_dl'] ? 'sí' : 'no',
                $row['misma_fn'] ? 'sí' : 'no',
                $row['n_notas'],
                $row['n_asis'],
                $row['bucket']
            );
        }
        echo "\n";
    }
}

if ($fase === 'vacios' || $fase === 'todo') {
    echo "=== D vacíos borrables (total {$r['D_vacio_borrable']}) ===\n";
    foreach ($report['vacios'] as $row) {
        echo sprintf(
            "  id=%s dl=%s | %s %s, %s | nac=%s\n",
            $row['id_nom'],
            $row['dl'] ?? '',
            $row['apellido1'] ?? '',
            $row['apellido2'] ?? '',
            $row['nom'] ?? '',
            $row['f_nacimiento'] ?? ''
        );
    }
}

exit(0);
