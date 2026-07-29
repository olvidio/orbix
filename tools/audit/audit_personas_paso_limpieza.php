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
 * Producción (usuario SO postgres, sin ConfigDB / .inc) — modo por defecto:
 *   sudo -u postgres php tools/audit/audit_personas_paso_limpieza.php --fase=resumen
 *   sudo -u postgres php tools/audit/audit_personas_paso_limpieza.php \
 *     --db-sv=pruebas-sv --db-sv-e=pruebas-sv-e --db-comun=pruebas-comun
 *   # Asistencias se leen de sv-e (d_asistentes_de_paso / d_asistentes_ex).
 *   # --db-sv-e se deduce de --db-sv si no se pasa (sv→sv-e, pruebas-sv→pruebas-sv-e).
 *
 * Desarrollo (ConfigDB del repo):
 *   php tools/audit/audit_personas_paso_limpieza.php --configdb --fase=resumen
 *
 * Otras opciones:
 *   --curso-ini=2025-10-01
 *   --fase=resumen|buckets|duplicados|vs-global|vacios|historial|notas|todo
 *   --schema-paso=restov
 *   --json  --limit=30
 *   --pg  (explícito; es el default)
 *   --dry-run  con --fase=vacios|historial|notas
 *   --apply    con --fase=vacios|historial|notas
 *
 * historial: borra asistencias con f_ini < curso_ini (no toca curso ni bucket A).
 *   Si hay gemelo en global.personas, copia antes a d_asistentes_dl del esquema orbix.
 *   No borra la persona; tras historial, los C suelen pasar a D → --fase=vacios.
 *
 * notas: solo personas con pareja en global.personas. Copia/reasigna notas al id_orbix
 *   (no duplica si ya existe id_orbix+id_asignatura). Solo entonces borra la fila de paso.
 *   Sin pareja → no se toca ninguna nota.
 *
 * SQL de referencia: tools/audit/sql/personas_paso_limpieza.sql
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$database = 'sv';
$dbSvName = null;
$dbSvEName = null;
$dbComunName = 'comun';
$cursoIni = '2025-10-01';
$fase = 'resumen'; // resumen|buckets|duplicados|vs-global|vacios|todo
$jsonOutput = false;
$limit = 0;
$pasoSchema = null; // restov / restof por defecto según database
$usePg = true; // default: PDO directo (prod como postgres, sin ConfigDB)
$dryRun = false;
$apply = false;

foreach ($argv as $arg) {
    if ($arg === '--pg') {
        $usePg = true;
    }
    if ($arg === '--configdb') {
        $usePg = false;
    }
    if ($arg === '--dry-run') {
        $dryRun = true;
    }
    if ($arg === '--apply') {
        $apply = true;
    }
    if (str_starts_with($arg, '--database=')) {
        $database = substr($arg, strlen('--database='));
    }
    if (str_starts_with($arg, '--db-sv=')) {
        $dbSvName = substr($arg, strlen('--db-sv='));
    }
    if (str_starts_with($arg, '--db-sv-e=')) {
        $dbSvEName = substr($arg, strlen('--db-sv-e='));
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

if ($dryRun && $apply) {
    fwrite(STDERR, "No combine --dry-run y --apply.\n");
    exit(1);
}

$fasesOk = ['resumen', 'buckets', 'duplicados', 'vs-global', 'vacios', 'historial', 'notas', 'todo'];
if (!in_array($fase, $fasesOk, true)) {
    fwrite(STDERR, 'fase inválida: ' . implode('|', $fasesOk) . "\n");
    exit(1);
}

$dbSvName ??= $database;
$dbSvEName ??= deriveSvEName($dbSvName);
$publicSchema = $database === 'sf' ? 'publicf' : 'publicv';
$pasoSchema ??= ($database === 'sf' ? 'restof' : 'restov');

/**
 * sv → sv-e ; pruebas-sv → pruebas-sv-e
 */
function deriveSvEName(string $dbSv): string
{
    if (str_ends_with($dbSv, '-e')) {
        return $dbSv;
    }

    return $dbSv . '-e';
}

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

/**
 * Localiza tablas de asistencias relevantes en una BD.
 *
 * @return list<string> identificadores "schema.tabla" ya entrecomillados
 */
function discoverAsisTables(PDO $pdo, string $publicSchema, string $pasoSchema): array
{
    $found = [];
    $candidates = [
        [$publicSchema, 'd_asistentes_de_paso'],
        [$publicSchema, 'd_asistentes_all'],
        [$pasoSchema, 'd_asistentes_ex'],
    ];
    foreach ($candidates as [$schema, $tabla]) {
        $reg = $pdo->query('SELECT to_regclass(' . $pdo->quote("{$schema}.{$tabla}") . ')')->fetchColumn();
        if ($reg !== false && $reg !== null && $reg !== '') {
            $found[] = qIdent($schema) . '.' . $tabla;
        }
    }

    // d_asistentes_ex en cualquier esquema (DL / resto)
    $stmt = $pdo->query(
        "SELECT n.nspname
         FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
         WHERE c.relname = 'd_asistentes_ex'
           AND c.relkind IN ('r', 'p')
           AND n.nspname NOT LIKE 'pg_%'
           AND n.nspname <> 'information_schema'
         ORDER BY n.nspname"
    );
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) ?: [] as $schema) {
        $schema = (string) $schema;
        $ref = qIdent($schema) . '.d_asistentes_ex';
        if (!in_array($ref, $found, true)) {
            $found[] = $ref;
        }
    }

    return $found;
}

/**
 * @param list<int> $idsPaso
 * @param array<int, list<int>> $activsByNom
 * @param array<int, array<string, mixed>> $pasoById
 * @param list<string> $asisTables
 * @return array{0: array<int, list<int>>, 1: array<int, array<string, mixed>>}
 */
function loadAsistencias(
    PDO $pdo,
    array $asisTables,
    array $idsPaso,
    array $activsByNom,
    array $pasoById,
): array {
    if ($asisTables === [] || $idsPaso === []) {
        return [$activsByNom, $pasoById];
    }

    foreach ($asisTables as $from) {
        foreach (array_chunk($idsPaso, 800) as $chunk) {
            $in = implode(',', array_map('intval', $chunk));
            $stmt = $pdo->query("SELECT id_nom, id_activ FROM {$from} WHERE id_nom IN ({$in})");
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
    }

    return [$activsByNom, $pasoById];
}

try {
    $pdoSvE = null;
    if ($usePg) {
        $pdoSv = pdoPg($dbSvName);
        $pdoComun = pdoPg($dbComunName);
        try {
            $pdoSvE = pdoPg($dbSvEName);
        } catch (Throwable $e) {
            fwrite(STDERR, "AVISO: no se pudo abrir db-sv-e={$dbSvEName}: " . $e->getMessage() . "\n");
            fwrite(STDERR, "        Las asistencias pueden quedar sin inventariar.\n");
        }
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
        try {
            $pdoSvE = (new \src\shared\infrastructure\persistence\DBConnection(
                (new \src\shared\infrastructure\persistence\ConfigDB('sv-e'))->getEsquema(
                    $database === 'sf' ? 'publicf' : 'publicv-e'
                )
            ))->getPDO();
        } catch (Throwable $e) {
            // sf no usa publicv-e; reintentar publicv / publicf
            try {
                $pdoSvE = (new \src\shared\infrastructure\persistence\DBConnection(
                    (new \src\shared\infrastructure\persistence\ConfigDB('sv-e'))->getEsquema($publicSchema)
                ))->getPDO();
            } catch (Throwable $e2) {
                fwrite(STDERR, "AVISO: ConfigDB sv-e no disponible: " . $e2->getMessage() . "\n");
            }
        }
        $connMode = 'configdb';
        $dbSvName = $database;
        $dbComunName = 'comun';
        $dbSvEName = 'sv-e';
    }
    $pdoSv->exec("SET statement_timeout = '300s'");
    $pdoComun->exec("SET statement_timeout = '120s'");
    if ($pdoSvE instanceof PDO) {
        $pdoSvE->exec("SET statement_timeout = '300s'");
    }

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

    // --- Asistencias (sv interior + sv-e exterior) ---
    /** @var array<int, list<int>> $activsByNom */
    $activsByNom = [];
    /** @var list<string> $asisTablesLabeled */
    $asisTablesLabeled = [];

    $asisOnSv = discoverAsisTables($pdoSv, $publicSchema, $pasoSchema);
    foreach ($asisOnSv as $t) {
        $asisTablesLabeled[] = "{$dbSvName}:{$t}";
    }
    [$activsByNom, $pasoById] = loadAsistencias($pdoSv, $asisOnSv, $idsPaso, $activsByNom, $pasoById);

    $asisOnSvE = [];
    if ($pdoSvE instanceof PDO) {
        $asisOnSvE = discoverAsisTables($pdoSvE, $publicSchema, $pasoSchema);
        // En sv-e el público a veces es publicv-e
        if ($database !== 'sf') {
            $extra = discoverAsisTables($pdoSvE, 'publicv-e', $pasoSchema);
            foreach ($extra as $t) {
                if (!in_array($t, $asisOnSvE, true)) {
                    $asisOnSvE[] = $t;
                }
            }
        }
        foreach ($asisOnSvE as $t) {
            $asisTablesLabeled[] = "{$dbSvEName}:{$t}";
        }
        [$activsByNom, $pasoById] = loadAsistencias($pdoSvE, $asisOnSvE, $idsPaso, $activsByNom, $pasoById);
    }

    $asisVerificado = $asisTablesLabeled !== [];
    if (!$asisVerificado) {
        fwrite(STDERR, "AVISO: no se encontró ninguna tabla d_asistentes_* en {$dbSvName} ni {$dbSvEName}.\n");
        fwrite(STDERR, "       El bucket D NO es fiable para borrar (solo = sin notas).\n");
    }

    foreach ($activsByNom as $id => $acts) {
        $pasoById[$id]['id_activs'] = array_values(array_unique($acts));
        // n_asis se incrementó por fila; recalcular como nº de actividades distintas
        $pasoById[$id]['n_asis'] = count($pasoById[$id]['id_activs']);
    }

    // --- Actividades: f_ini (comun) ---
    /** @var array<int, string> $fIniByActiv */
    $fIniByActiv = [];
    /** @var array<int, true> $activCurso */
    $activCurso = [];
    $allActivIds = [];
    foreach ($activsByNom as $acts) {
        foreach ($acts as $a) {
            $allActivIds[$a] = true;
        }
    }
    if ($allActivIds !== []) {
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
                $fIniByActiv[$idActiv] = $fIni;
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
        'db_sv_e' => $dbSvEName,
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
        'asis_verificado' => $asisVerificado,
        'asis_tablas' => $asisTablesLabeled,
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

    // --- vs global.personas ---
    $vsGlobal = [];
    /** @var array<int, array{id_orbix: int, dl_orbix: mixed, id_schema: int|null, misma_dl: bool, misma_fn: bool}> $mejorMatch */
    $mejorMatch = [];
    $stmt = $pdoSv->query(
        "SELECT id_nom, id_schema, dl, id_tabla, apellido1, apellido2, nom,
                f_nacimiento::text AS f_nacimiento, situacion
         FROM global.personas"
    );
    /** @var array<string, list<array<string, mixed>>> $globalByKey */
    $globalByKey = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $g) {
        if (normKey($g['apellido1'] ?? '') === '' || normKey($g['nom'] ?? '') === '') {
            continue;
        }
        $k = normKey($g['apellido1'] ?? '') . '|' . normKey($g['apellido2'] ?? '')
            . '|' . normKey($g['nom'] ?? '');
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
            $mismaDl = normKey($p['dl'] ?? '') === normKey($g['dl'] ?? '');
            $mismaFn = $p['f_nacimiento'] !== null && $p['f_nacimiento'] !== ''
                && $g['f_nacimiento'] !== null && $g['f_nacimiento'] !== ''
                && $p['f_nacimiento'] === $g['f_nacimiento'];
            $cand = [
                'id_paso' => (int) $p['id_nom'],
                'dl_paso' => $p['dl'],
                'bucket' => $p['bucket'],
                'id_orbix' => (int) $g['id_nom'],
                'dl_orbix' => $g['dl'],
                'id_schema' => isset($g['id_schema']) ? (int) $g['id_schema'] : null,
                'id_tabla_orbix' => $g['id_tabla'],
                'apellido1' => $p['apellido1'],
                'apellido2' => $p['apellido2'],
                'nom' => $p['nom'],
                'fn_paso' => $p['f_nacimiento'],
                'fn_orbix' => $g['f_nacimiento'],
                'misma_dl' => $mismaDl,
                'misma_fn' => $mismaFn,
                'n_notas' => $p['n_notas'],
                'n_asis' => $p['n_asis'],
            ];
            $vsGlobal[] = $cand;
            $idPaso = (int) $p['id_nom'];
            if (!isset($mejorMatch[$idPaso])) {
                $mejorMatch[$idPaso] = $cand;
            } else {
                $cur = $mejorMatch[$idPaso];
                $scoreNew = [(int) $mismaDl, (int) $mismaFn];
                $scoreCur = [(int) $cur['misma_dl'], (int) $cur['misma_fn']];
                if ($scoreNew > $scoreCur) {
                    $mejorMatch[$idPaso] = $cand;
                }
            }
        }
    }
    usort(
        $vsGlobal,
        static function (array $a, array $b): int {
            return [$b['misma_dl'], $b['misma_fn'], $a['apellido1'], $a['nom'], $a['id_paso']]
                <=> [$a['misma_dl'], $a['misma_fn'], $b['apellido1'], $b['nom'], $b['id_paso']];
        }
    );

    /** @var array<int, string> $schemaById */
    $schemaById = [];
    foreach ($pdoSv->query('SELECT id, schema FROM public.db_idschema')->fetchAll(PDO::FETCH_ASSOC) ?: [] as $r) {
        $schemaById[(int) $r['id']] = (string) $r['schema'];
    }

    $vacios = array_values(array_filter(
        $buckets['D'],
        static fn(array $r): bool => true
    ));
    usort(
        $vacios,
        static fn(array $a, array $b): int => [$a['apellido1'], $a['apellido2'], $a['nom'], $a['id_nom']]
            <=> [$b['apellido1'], $b['apellido2'], $b['nom'], $b['id_nom']]
    );

    // Inventario historial (asis no-curso, fuera de bucket A)
    $historialPlan = [
        'filas_borrar_sin_match' => 0,
        'filas_copiar_y_borrar' => 0,
        'filas_omitidas_sin_destino' => 0,
        'personas_sin_match' => 0,
        'personas_con_match' => 0,
        'muestra' => [],
    ];
    $idsConHistorial = [];
    foreach ($pasoById as $id => $row) {
        if ($row['bucket'] === 'A' || $row['n_asis'] <= 0) {
            continue;
        }
        $idsConHistorial[] = $id;
    }
    $historialPlan['personas_sin_match'] = count(array_filter(
        $idsConHistorial,
        static fn(int $id): bool => !isset($mejorMatch[$id])
    ));
    $historialPlan['personas_con_match'] = count(array_filter(
        $idsConHistorial,
        static fn(int $id): bool => isset($mejorMatch[$id])
    ));

    // --- Mutaciones (--dry-run / --apply) ---
    $borrado = [
        'modo' => 'ninguno',
        'candidatos' => 0,
        'borrados' => 0,
        'telecos_borrados' => 0,
        'omitidos_recheck' => 0,
        'ids' => [],
    ];
    $historialResult = [
        'modo' => 'ninguno',
        'copiadas' => 0,
        'borradas' => 0,
        'omitidas' => 0,
        'ya_existian_en_orbix' => 0,
    ];
    $notasPlan = [
        'personas_con_notas_y_match' => 0,
        'personas_con_notas_sin_match' => 0,
        'filas_update' => 0,
        'filas_insert_borrar' => 0,
        'filas_borrar_ya_existe' => 0,
        'filas_omitidas' => 0,
        'muestra' => [],
    ];
    $notasResult = [
        'modo' => 'ninguno',
        'actualizadas' => 0,
        'insertadas' => 0,
        'borradas' => 0,
        'ya_existian' => 0,
        'omitidas' => 0,
    ];

    $idsConNotas = [];
    foreach ($pasoById as $id => $row) {
        if ($row['n_notas'] > 0) {
            $idsConNotas[] = $id;
        }
    }
    $notasPlan['personas_con_notas_y_match'] = count(array_filter(
        $idsConNotas,
        static fn(int $id): bool => isset($mejorMatch[$id])
    ));
    $notasPlan['personas_con_notas_sin_match'] = count(array_filter(
        $idsConNotas,
        static fn(int $id): bool => !isset($mejorMatch[$id])
    ));

    if ($dryRun || $apply) {
        if (!in_array($fase, ['vacios', 'historial', 'notas'], true)) {
            throw new RuntimeException(
                '--dry-run / --apply solo con --fase=vacios|historial|notas.'
            );
        }
        if (in_array($fase, ['vacios', 'historial'], true) && !$asisVerificado) {
            throw new RuntimeException(
                'No se puede mutar: asis_verificado=false. Revise --db-sv-e.'
            );
        }

        if ($fase === 'vacios') {
            $borrado['modo'] = $apply ? 'apply' : 'dry-run';

            /** @var list<int> $idsD */
            $idsD = array_map(static fn(array $r): int => (int) $r['id_nom'], $vacios);
            if ($limit > 0) {
                $idsD = array_slice($idsD, 0, $limit);
            }

            $idsOk = [];
            foreach ($idsD as $id) {
                $row = $pasoById[$id] ?? null;
                if ($row === null) {
                    $borrado['omitidos_recheck']++;
                    continue;
                }
                if ($row['n_notas'] > 0 || $row['n_asis'] > 0 || $row['tiene_curso'] || $row['bucket'] !== 'D') {
                    $borrado['omitidos_recheck']++;
                    continue;
                }
                $idsOk[] = $id;
            }
            $borrado['ids'] = $idsOk;
            $borrado['candidatos'] = count($idsOk);

            $tieneTeleco = $pdoSv->query(
                'SELECT to_regclass(' . $pdoSv->quote("{$pasoSchema}.d_teleco_personas_ex") . ')'
            )->fetchColumn();

            if ($apply && $idsOk !== []) {
                $pdoSv->beginTransaction();
                try {
                    foreach (array_chunk($idsOk, 500) as $chunk) {
                        $in = implode(',', array_map('intval', $chunk));
                        if ($tieneTeleco) {
                            $nTel = $pdoSv->exec(
                                "DELETE FROM {$qPaso}.d_teleco_personas_ex WHERE id_nom IN ({$in})"
                            );
                            $borrado['telecos_borrados'] += $nTel !== false ? (int) $nTel : 0;
                        }
                        $n = $pdoSv->exec(
                            "DELETE FROM {$qPaso}.p_de_paso_ex WHERE id_nom IN ({$in})"
                        );
                        $borrado['borrados'] += $n !== false ? (int) $n : 0;
                    }
                    $pdoSv->commit();
                } catch (Throwable $e) {
                    $pdoSv->rollBack();
                    throw $e;
                }
            }
        }

        if ($fase === 'historial') {
            $historialResult['modo'] = $apply ? 'apply' : 'dry-run';
            if (!$pdoSvE instanceof PDO) {
                throw new RuntimeException('historial requiere conexión a db-sv-e.');
            }

            // Tablas asis solo en sv-e (fuente de verdad de asistencias de paso)
            $asisSvE = discoverAsisTables($pdoSvE, $publicSchema, $pasoSchema);
            if ($database !== 'sf') {
                foreach (discoverAsisTables($pdoSvE, 'publicv-e', $pasoSchema) as $t) {
                    if (!in_array($t, $asisSvE, true)) {
                        $asisSvE[] = $t;
                    }
                }
            }
            // No operar en d_asistentes_all (padre). Usar ONLY para no ver hijos 2 veces.
            $asisSvE = array_values(array_filter(
                $asisSvE,
                static fn(string $t): bool => !str_ends_with($t, '.d_asistentes_all')
            ));

            $idsTarget = $idsConHistorial;
            if ($limit > 0) {
                $idsTarget = array_slice($idsTarget, 0, $limit);
            }
            $idSet = array_fill_keys($idsTarget, true);

            $acciones = [];
            $visto = []; // id_nom|id_activ para deduplicar
            foreach ($asisSvE as $from) {
                if ($idsTarget === []) {
                    break;
                }
                $fromOnly = 'ONLY ' . $from;
                foreach (array_chunk($idsTarget, 800) as $chunk) {
                    $in = implode(',', array_map('intval', $chunk));
                    $stmt = $pdoSvE->query("SELECT * FROM {$fromOnly} WHERE id_nom IN ({$in})");
                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $asisRow) {
                        $idNom = (int) $asisRow['id_nom'];
                        $idActiv = (int) $asisRow['id_activ'];
                        $dedupKey = $idNom . '|' . $idActiv;
                        if (isset($visto[$dedupKey])) {
                            continue;
                        }
                        if (!isset($idSet[$idNom])) {
                            continue;
                        }
                        $p = $pasoById[$idNom] ?? null;
                        if ($p === null || $p['bucket'] === 'A') {
                            continue;
                        }
                        if (isset($activCurso[$idActiv])) {
                            continue;
                        }
                        $fIni = $fIniByActiv[$idActiv] ?? '';
                        if ($fIni === '') {
                            $historialResult['omitidas']++;
                            continue;
                        }
                        if ($fIni >= $cursoIni) {
                            continue;
                        }

                        $visto[$dedupKey] = true;
                        $match = $mejorMatch[$idNom] ?? null;
                        $accion = [
                            'from' => $from,
                            'id_paso' => $idNom,
                            'id_activ' => $idActiv,
                            'f_ini' => $fIni,
                            'bucket' => $p['bucket'],
                            'id_orbix' => $match['id_orbix'] ?? null,
                            'schema_orbix' => null,
                            'tipo' => 'borrar',
                        ];
                        if ($match !== null) {
                            $schId = $match['id_schema'] ?? null;
                            $schName = ($schId !== null && isset($schemaById[$schId]))
                                ? $schemaById[$schId]
                                : null;
                            $accion['schema_orbix'] = $schName;
                            $destExists = $schName !== null && $pdoSvE->query(
                                'SELECT to_regclass(' . $pdoSvE->quote($schName . '.d_asistentes_dl') . ')'
                            )->fetchColumn();
                            if ($destExists) {
                                $accion['tipo'] = 'copiar_borrar';
                                $accion['dest'] = qIdent($schName) . '.d_asistentes_dl';
                                $historialPlan['filas_copiar_y_borrar']++;
                            } else {
                                $accion['tipo'] = 'omitir_sin_destino';
                                $historialPlan['filas_omitidas_sin_destino']++;
                            }
                        } else {
                            $historialPlan['filas_borrar_sin_match']++;
                        }
                        $acciones[] = $accion;
                        if (count($historialPlan['muestra']) < 30) {
                            $historialPlan['muestra'][] = $accion;
                        }
                    }
                }
            }

            if ($dryRun) {
                // plan ya en historialPlan / acciones
                $historialResult['borradas'] = count(array_filter(
                    $acciones,
                    static fn(array $a): bool => in_array($a['tipo'], ['borrar', 'copiar_borrar'], true)
                ));
                $historialResult['copiadas'] = count(array_filter(
                    $acciones,
                    static fn(array $a): bool => $a['tipo'] === 'copiar_borrar'
                ));
                $historialResult['omitidas'] += count(array_filter(
                    $acciones,
                    static fn(array $a): bool => $a['tipo'] === 'omitir_sin_destino'
                ));
            }

            if ($apply) {
                $pdoSvE->beginTransaction();
                try {
                    foreach ($acciones as $accion) {
                        if ($accion['tipo'] === 'omitir_sin_destino') {
                            $historialResult['omitidas']++;
                            continue;
                        }
                        $from = $accion['from'];
                        $idPaso = (int) $accion['id_paso'];
                        $idActiv = (int) $accion['id_activ'];

                        if ($accion['tipo'] === 'copiar_borrar') {
                            $idOrbix = (int) $accion['id_orbix'];
                            $dest = $accion['dest'];
                            // ¿ya existe?
                            $exists = $pdoSvE->query(
                                "SELECT 1 FROM {$dest} WHERE id_activ = {$idActiv} AND id_nom = {$idOrbix} LIMIT 1"
                            )->fetchColumn();
                            if ($exists) {
                                $historialResult['ya_existian_en_orbix']++;
                            } else {
                                // copiar columnas comunes
                                $src = $pdoSvE->query(
                                    "SELECT * FROM {$from} WHERE id_activ = {$idActiv} AND id_nom = {$idPaso} LIMIT 1"
                                )->fetch(PDO::FETCH_ASSOC);
                                if ($src === false) {
                                    $historialResult['omitidas']++;
                                    continue;
                                }
                                $src['id_nom'] = $idOrbix;
                                // id_schema del destino si la columna existe
                                $destCols = $pdoSvE->query(
                                    "SELECT column_name FROM information_schema.columns
                                     WHERE table_schema = " . $pdoSvE->quote(
                                         trim(explode('.', str_replace('"', '', $dest))[0])
                                     ) . "
                                       AND table_name = 'd_asistentes_dl'"
                                )->fetchAll(PDO::FETCH_COLUMN) ?: [];
                                $destCols = array_map('strval', $destCols);
                                $vals = [];
                                $cols = [];
                                foreach ($destCols as $col) {
                                    if (!array_key_exists($col, $src)) {
                                        continue;
                                    }
                                    $cols[] = $col;
                                    $vals[] = $src[$col];
                                }
                                if ($cols === []) {
                                    $historialResult['omitidas']++;
                                    continue;
                                }
                                $ph = [];
                                $bind = [];
                                foreach ($cols as $i => $col) {
                                    $ph[] = ':c' . $i;
                                    $bind[':c' . $i] = $vals[$i];
                                }
                                $sqlIns = 'INSERT INTO ' . $dest . ' (' . implode(',', $cols) . ')
                                           VALUES (' . implode(',', $ph) . ')';
                                $ins = $pdoSvE->prepare($sqlIns);
                                $ins->execute($bind);
                                $historialResult['copiadas']++;
                            }
                        }

                        $n = $pdoSvE->exec(
                            "DELETE FROM ONLY {$from} WHERE id_activ = {$idActiv} AND id_nom = {$idPaso}"
                        );
                        $historialResult['borradas'] += $n !== false ? (int) $n : 0;
                    }
                    $pdoSvE->commit();
                } catch (Throwable $e) {
                    $pdoSvE->rollBack();
                    throw $e;
                }
            }
        }

        if ($fase === 'notas') {
            $notasResult['modo'] = $apply ? 'apply' : 'dry-run';

            // Hijas de public*.e_notas
            $hijas = $pdoSv->query(
                "SELECT n.nspname AS schema, c.relname AS tabla
                 FROM pg_inherits i
                 JOIN pg_class c ON c.oid = i.inhrelid
                 JOIN pg_namespace n ON n.oid = c.relnamespace
                 JOIN pg_class p ON p.oid = i.inhparent
                 JOIN pg_namespace pn ON pn.oid = p.relnamespace
                 WHERE pn.nspname = " . $pdoSv->quote($publicSchema) . "
                   AND p.relname = 'e_notas'
                 ORDER BY 1, 2"
            )->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $idsTarget = array_values(array_filter(
                $idsConNotas,
                static fn(int $id): bool => isset($mejorMatch[$id])
            ));
            if ($limit > 0) {
                $idsTarget = array_slice($idsTarget, 0, $limit);
            }

            /** @var list<array<string, mixed>> $accionesNotas */
            $accionesNotas = [];
            if ($idsTarget !== []) {
                foreach ($hijas as $hija) {
                    $sch = (string) $hija['schema'];
                    $tab = (string) $hija['tabla'];
                    $from = qIdent($sch) . '.' . $tab;
                    foreach (array_chunk($idsTarget, 500) as $chunk) {
                        $in = implode(',', array_map('intval', $chunk));
                        $stmt = $pdoSv->query(
                            "SELECT * FROM ONLY {$from} WHERE id_nom IN ({$in})"
                        );
                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $nota) {
                            $idPaso = (int) $nota['id_nom'];
                            $idAsig = (int) $nota['id_asignatura'];
                            $match = $mejorMatch[$idPaso] ?? null;
                            if ($match === null) {
                                continue; // no tocar sin pareja
                            }
                            $idOrbix = (int) $match['id_orbix'];
                            $ya = $pdoSv->query(
                                "SELECT 1 FROM {$qPublic}.e_notas
                                 WHERE id_nom = {$idOrbix} AND id_asignatura = {$idAsig}
                                 LIMIT 1"
                            )->fetchColumn();

                            $accion = [
                                'from' => $from,
                                'tabla' => $tab,
                                'schema' => $sch,
                                'id_paso' => $idPaso,
                                'id_orbix' => $idOrbix,
                                'id_asignatura' => $idAsig,
                                'id_nivel' => $nota['id_nivel'] ?? null,
                                'tipo' => 'omitir',
                                'dest' => null,
                            ];

                            if ($ya) {
                                $accion['tipo'] = 'borrar_ya_existe';
                                $notasPlan['filas_borrar_ya_existe']++;
                            } elseif ($tab === 'e_notas_dl' || $tab === 'e_notas_otra_region_stgr') {
                                // Ya está en tabla de DL/región: reasignar id_nom
                                $accion['tipo'] = 'update_id_nom';
                                $notasPlan['filas_update']++;
                            } elseif ($tab === 'e_notas_ex') {
                                $schId = $match['id_schema'] ?? null;
                                $schName = ($schId !== null && isset($schemaById[$schId]))
                                    ? $schemaById[$schId]
                                    : null;
                                $destOk = $schName !== null && $pdoSv->query(
                                    'SELECT to_regclass(' . $pdoSv->quote($schName . '.e_notas_dl') . ')'
                                )->fetchColumn();
                                if ($destOk) {
                                    $accion['tipo'] = 'insert_dl_borrar';
                                    $accion['dest'] = qIdent($schName) . '.e_notas_dl';
                                    $accion['dest_schema'] = $schName;
                                    $accion['dest_id_schema'] = $schId;
                                    $notasPlan['filas_insert_borrar']++;
                                } else {
                                    $accion['tipo'] = 'omitir';
                                    $notasPlan['filas_omitidas']++;
                                }
                            } else {
                                $accion['tipo'] = 'omitir';
                                $notasPlan['filas_omitidas']++;
                            }

                            $accion['_row'] = $nota;
                            $accionesNotas[] = $accion;
                            if (count($notasPlan['muestra']) < 30) {
                                $m = $accion;
                                unset($m['_row']);
                                $notasPlan['muestra'][] = $m;
                            }
                        }
                    }
                }
            }

            if ($dryRun) {
                foreach ($accionesNotas as $a) {
                    match ($a['tipo']) {
                        'update_id_nom' => $notasResult['actualizadas']++,
                        'insert_dl_borrar' => $notasResult['insertadas']++,
                        'borrar_ya_existe' => $notasResult['ya_existian']++,
                        default => $notasResult['omitidas']++,
                    };
                    if (in_array($a['tipo'], ['update_id_nom', 'insert_dl_borrar', 'borrar_ya_existe'], true)) {
                        $notasResult['borradas']++; // update cuenta como “mover”; insert+borrar también
                    }
                }
                // En dry-run, para update no hay borrado aparte; ajustar mensaje:
                // borradas = filas que dejan de estar bajo id_paso
                $notasResult['borradas'] = $notasResult['actualizadas']
                    + $notasResult['insertadas']
                    + $notasResult['ya_existian'];
            }

            if ($apply) {
                $pdoSv->beginTransaction();
                try {
                    foreach ($accionesNotas as $accion) {
                        $from = $accion['from'];
                        $idPaso = (int) $accion['id_paso'];
                        $idOrbix = (int) $accion['id_orbix'];
                        $idAsig = (int) $accion['id_asignatura'];
                        $nota = $accion['_row'];

                        if ($accion['tipo'] === 'omitir') {
                            $notasResult['omitidas']++;
                            continue;
                        }

                        if ($accion['tipo'] === 'borrar_ya_existe') {
                            $n = $pdoSv->exec(
                                "DELETE FROM ONLY {$from}
                                 WHERE id_nom = {$idPaso} AND id_asignatura = {$idAsig}"
                            );
                            $notasResult['ya_existian']++;
                            $notasResult['borradas'] += $n !== false ? (int) $n : 0;
                            continue;
                        }

                        if ($accion['tipo'] === 'update_id_nom') {
                            $n = $pdoSv->exec(
                                "UPDATE ONLY {$from}
                                 SET id_nom = {$idOrbix}
                                 WHERE id_nom = {$idPaso} AND id_asignatura = {$idAsig}"
                            );
                            $notasResult['actualizadas'] += $n !== false ? (int) $n : 0;
                            continue;
                        }

                        if ($accion['tipo'] === 'insert_dl_borrar') {
                            $dest = (string) $accion['dest'];
                            $yaDest = $pdoSv->query(
                                "SELECT 1 FROM {$dest}
                                 WHERE id_nom = {$idOrbix} AND id_asignatura = {$idAsig}
                                 LIMIT 1"
                            )->fetchColumn();
                            if (!$yaDest) {
                                $src = $nota;
                                $src['id_nom'] = $idOrbix;
                                if (isset($accion['dest_id_schema']) && $accion['dest_id_schema'] !== null) {
                                    $src['id_schema'] = (int) $accion['dest_id_schema'];
                                }
                                $destSchema = (string) $accion['dest_schema'];
                                $destCols = $pdoSv->query(
                                    "SELECT column_name FROM information_schema.columns
                                     WHERE table_schema = " . $pdoSv->quote($destSchema) . "
                                       AND table_name = 'e_notas_dl'
                                     ORDER BY ordinal_position"
                                )->fetchAll(PDO::FETCH_COLUMN) ?: [];
                                $cols = [];
                                $bind = [];
                                $ph = [];
                                $i = 0;
                                foreach ($destCols as $col) {
                                    $col = (string) $col;
                                    if (!array_key_exists($col, $src)) {
                                        continue;
                                    }
                                    $cols[] = $col;
                                    $key = ':c' . $i;
                                    $ph[] = $key;
                                    $bind[$key] = $src[$col];
                                    $i++;
                                }
                                if ($cols === []) {
                                    $notasResult['omitidas']++;
                                    continue;
                                }
                                $ins = $pdoSv->prepare(
                                    'INSERT INTO ' . $dest . ' (' . implode(',', $cols) . ')
                                     VALUES (' . implode(',', $ph) . ')'
                                );
                                $ins->execute($bind);
                                $notasResult['insertadas']++;
                            } else {
                                $notasResult['ya_existian']++;
                            }
                            $n = $pdoSv->exec(
                                "DELETE FROM ONLY {$from}
                                 WHERE id_nom = {$idPaso} AND id_asignatura = {$idAsig}"
                            );
                            $notasResult['borradas'] += $n !== false ? (int) $n : 0;
                        }
                    }
                    $pdoSv->commit();
                } catch (Throwable $e) {
                    $pdoSv->rollBack();
                    throw $e;
                }
            }
        }
    }

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
        'historial_plan' => $historialPlan,
        'notas_plan' => $notasPlan,
        'borrado' => $borrado,
        'historial' => $historialResult,
        'notas' => $notasResult,
    ];
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    if (!$usePg) {
        fwrite(STDERR, "Hint: en producción use PDO (default) o --pg; ConfigDB solo con --configdb.\n");
    } else {
        fwrite(STDERR, "Hint: revise --db-sv / --db-comun / --db-sv-e y PGHOST/PGPORT.\n");
    }
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
        'historial' => [
            'plan' => $report['historial_plan'],
            'resultado' => $report['historial'],
        ],
        'notas' => [
            'plan' => $report['notas_plan'],
            'resultado' => $report['notas'],
        ],
        default => $report,
    };
    echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

$r = $report['resumen'];
echo "Personas de paso — limpieza (solo lectura)\n";
echo "conn={$r['conn']} db_sv={$r['db_sv']} db_sv_e={$r['db_sv_e']} db_comun={$r['db_comun']} paso={$r['paso_schema']} curso_ini={$r['curso_ini']}\n\n";

if ($fase === 'resumen' || $fase === 'todo' || $fase === 'buckets') {
    echo "=== Resumen buckets ===\n";
    echo "  total situacion=A:     {$r['total_paso_A']}\n";
    echo "  A protegido_curso:     {$r['A_protegido_curso']}\n";
    echo "  B con_notas:           {$r['B_con_notas']}\n";
    echo "  C solo_historial:      {$r['C_solo_historial']}\n";
    echo "  D vacio_borrable:      {$r['D_vacio_borrable']}\n";
    echo '  asis_verificado:       ' . (!empty($r['asis_verificado']) ? 'sí' : 'NO') . "\n";
    echo '  tablas asis: ' . (empty($r['asis_tablas']) ? '(ninguna)' : implode(', ', $r['asis_tablas'])) . "\n";
    if (empty($r['asis_verificado'])) {
        echo "  !! Sin asistencias inventariadas: NO borres el bucket D todavía.\n";
    }
    echo "\n";
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
    echo "\n";
}

if (($dryRun || $apply) && isset($report['borrado']) && $fase === 'vacios') {
    $b = $report['borrado'];
    echo "=== Borrado bucket D (modo={$b['modo']}) ===\n";
    echo "  candidatos OK: {$b['candidatos']}\n";
    echo "  omitidos recheck: {$b['omitidos_recheck']}\n";
    if ($dryRun) {
        echo "  (dry-run: no se ha borrado nada)\n";
        $muestra = array_slice($b['ids'], 0, $limit > 0 ? $limit : 20);
        foreach ($muestra as $id) {
            echo "  DELETE id_nom={$id}\n";
        }
        if (count($b['ids']) > count($muestra)) {
            echo '  ... y ' . (count($b['ids']) - count($muestra)) . " más\n";
        }
    } else {
        echo "  borrados p_de_paso_ex: {$b['borrados']}\n";
        echo "  borrados telecos_ex:   {$b['telecos_borrados']}\n";
    }
}

if ($fase === 'historial' || (($dryRun || $apply) && $fase === 'historial')) {
    $hp = $report['historial_plan'];
    $hr = $report['historial'];
    echo "=== Historial asistencias (f_ini < {$r['curso_ini']}; no toca bucket A ni curso) ===\n";
    echo "  personas B/C con asis sin match orbix: {$hp['personas_sin_match']}\n";
    echo "  personas B/C con asis con match orbix: {$hp['personas_con_match']}\n";
    if ($hr['modo'] !== 'ninguno') {
        echo "  modo={$hr['modo']} copiadas={$hr['copiadas']} borradas={$hr['borradas']}"
            . " omitidas={$hr['omitidas']} ya_en_orbix={$hr['ya_existian_en_orbix']}\n";
        echo "  plan filas: borrar_sin_match≈{$hp['filas_borrar_sin_match']}"
            . " copiar_y_borrar≈{$hp['filas_copiar_y_borrar']}"
            . " omitir_sin_destino≈{$hp['filas_omitidas_sin_destino']}\n";
        if ($dryRun) {
            echo "  (dry-run: no se ha escrito nada)\n";
            foreach (array_slice($hp['muestra'], 0, 20) as $a) {
                echo sprintf(
                    "  %s paso=%s activ=%s → orbix=%s dest=%s f_ini=%s\n",
                    $a['tipo'],
                    $a['id_paso'],
                    $a['id_activ'],
                    $a['id_orbix'] ?? '-',
                    $a['dest'] ?? ($a['schema_orbix'] ?? '-'),
                    $a['f_ini']
                );
            }
        }
    } else {
        echo "  (use --dry-run o --apply para planificar/ejecutar el borrado de historial)\n";
    }
}

if ($fase === 'notas') {
    $np = $report['notas_plan'];
    $nr = $report['notas'];
    echo "=== Notas → pareja global.personas (sin perder filas) ===\n";
    echo "  personas con notas + match: {$np['personas_con_notas_y_match']}\n";
    echo "  personas con notas SIN match (no se tocan): {$np['personas_con_notas_sin_match']}\n";
    if ($nr['modo'] !== 'ninguno') {
        echo "  modo={$nr['modo']} update={$nr['actualizadas']} insert={$nr['insertadas']}"
            . " borradas={$nr['borradas']} ya_existian={$nr['ya_existian']} omitidas={$nr['omitidas']}\n";
        echo "  plan: update≈{$np['filas_update']} insert_dl≈{$np['filas_insert_borrar']}"
            . " borrar_ya_existe≈{$np['filas_borrar_ya_existe']} omitir≈{$np['filas_omitidas']}\n";
        if ($dryRun) {
            echo "  (dry-run: no se ha escrito nada)\n";
            foreach (array_slice($np['muestra'], 0, 20) as $a) {
                echo sprintf(
                    "  %s paso=%s asig=%s → orbix=%s from=%s dest=%s\n",
                    $a['tipo'],
                    $a['id_paso'],
                    $a['id_asignatura'],
                    $a['id_orbix'],
                    $a['from'],
                    $a['dest'] ?? '-'
                );
            }
        }
    } else {
        echo "  (use --dry-run o --apply; solo actúa con pareja en global.personas)\n";
    }
}

exit(0);
