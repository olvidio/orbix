#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Auditoría / dry-run: mapa prefijo de acta → esquema destino para repatriar
 * `e_notas_otra_region_stgr` → `e_notas_dl`.
 *
 * La aplicación en BD (local y producción) va por migraciones web:
 *   db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sv.sql
 *   db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sf.sql
 * (devel_db_admin → Migraciones). Mantener el mapa alineado con esas migraciones
 * y con `tools/fix/data/esquemas_dl_fusionados.php`.
 *
 * Uso:
 *   php tools/fix/fix_notas_otra_region_a_acta.php
 *   php tools/fix/fix_notas_otra_region_a_acta.php --database=sv --por-prefijo
 *
 * @see docs/dev/notas_modelo_acta.md
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
require $root . '/src/shared/global_header.inc';

use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

$database = 'sv';
$apply = in_array('--apply', $argv, true);
$porPrefijo = in_array('--por-prefijo', $argv, true);
$limit = 0;

if ($apply) {
    fwrite(STDERR, "AVISO: --apply del CLI está deprecado.\n");
    fwrite(STDERR, "Aplicar desde la web: devel_db_admin → Migraciones →\n");
    fwrite(STDERR, "  202607211300_repatriar_notas_otra_region_a_acta__sv.sql (o __sf).\n");
    exit(1);
}

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--database=')) {
        $database = substr($arg, strlen('--database='));
    }
    if (str_starts_with($arg, '--limit=')) {
        $limit = max(0, (int) substr($arg, strlen('--limit=')));
    }
}

ConfigGlobal::setTest_mode(true);
$suffix = ($database === 'sf') ? 'f' : 'v';
putenv('UBICACION=' . ($database === 'sf' ? 'sf' : 'sv'));
$publicSchema = 'public' . $suffix;

/** @var array<string, string> $fusiones base→base */
$fusiones = require $root . '/tools/fix/data/esquemas_dl_fusionados.php';
if (!is_array($fusiones)) {
    fwrite(STDERR, "Mapa de fusiones inválido.\n");
    exit(1);
}

/**
 * Quita sufijo sfsv (`v`/`f`). No altera bases que ya terminan en v/f (p. ej. `H-dlv`).
 *
 * @param array<string, string> $basesKnownLower lower(base) => base canónico
 */
function esquemaBase(string $esquema, array $basesKnownLower = []): string
{
    $low = strtolower($esquema);
    if ($basesKnownLower !== [] && isset($basesKnownLower[$low])) {
        return $basesKnownLower[$low];
    }
    if (preg_match('/^(.*)[vf]$/', $esquema, $m) !== 1) {
        return $esquema;
    }
    $sin = $m[1];
    if ($basesKnownLower !== [] && isset($basesKnownLower[strtolower($sin)])) {
        return $basesKnownLower[strtolower($sin)];
    }
    // Sin catálogo: asumir sufijo sfsv (H-dlpv → H-dlp; OJO H-dlv → H-dl si no está en catálogo)
    return $sin;
}

/** @param array<string, string> $fusiones */
function aplicarFusion(string $esquemaConSufijo, array $fusiones, string $suffix, array $basesKnownLower = []): string
{
    $base = esquemaBase($esquemaConSufijo, $basesKnownLower);
    $destinoBase = $fusiones[$base] ?? $base;

    return $destinoBase . $suffix;
}

/**
 * Prefijo del acta → base de esquema (H-dlx o Reg-crReg).
 *
 * @param array<string, string> $basesLower lower(base) => base canónico en db_idschema / fusiones
 * @param array<string, string> $fusiones
 */
function destinoDesdePrefijoActa(string $acta, array $basesLower, array $fusiones, string $suffix): ?array
{
    $acta = trim($acta);
    if ($acta === '') {
        return null;
    }
    // "fin bienio" y similares no son número de acta de DL
    if (preg_match('/^fin\b/i', $acta) === 1) {
        return null;
    }

    $pref = trim(explode(' ', $acta, 2)[0]);
    $prefLower = strtolower($pref);

    $candidatoBase = null;
    $via = 'acta.prefijo';

    if (str_starts_with($prefLower, 'cr') && strlen($prefLower) > 2) {
        $codigo = substr($prefLower, 2); // crGalbel → galbel
        foreach ($basesLower as $low => $canon) {
            if (!str_contains($low, '-cr')) {
                continue;
            }
            $tail = strtolower(preg_replace('/^cr/', '', explode('-', $canon, 2)[1] ?? '') ?? '');
            if ($tail === $codigo) {
                $candidatoBase = $canon;
                break;
            }
        }
    } elseif (isset($basesLower['h-' . $prefLower])) {
        $candidatoBase = $basesLower['h-' . $prefLower];
    } else {
        // Ch → Ch-crCh ; Iers → Iers-crIers ; dly → M-dly
        foreach ($basesLower as $low => $canon) {
            if ($low === $prefLower . '-cr' . $prefLower) {
                $candidatoBase = $canon;
                break;
            }
            if (str_ends_with($low, '-' . $prefLower)) {
                $candidatoBase = $canon;
                break;
            }
            if (explode('-', $low)[0] === $prefLower && str_contains($low, '-cr')) {
                $candidatoBase = $canon;
                break;
            }
        }
    }

    // DL absorbida: ya no está en db_idschema, pero sí en el mapa de fusiones (p. ej. H-dlv → H-dlal)
    if ($candidatoBase === null) {
        $want = 'h-' . $prefLower;
        foreach ($fusiones as $from => $_to) {
            if (strtolower((string) $from) === $want) {
                $candidatoBase = (string) $from;
                $via = 'acta.prefijo+fusion';
                break;
            }
        }
    }

    if ($candidatoBase === null) {
        return null;
    }

    $baseCanon = esquemaBase($candidatoBase, $basesLower);
    $conSufijo = $baseCanon . $suffix;
    $final = aplicarFusion($conSufijo, $fusiones, $suffix, $basesLower);

    return [
        'schema' => $final,
        'via' => $via,
        'detail' => "acta«{$acta}» prefijo={$pref} → {$baseCanon}"
            . ($conSufijo !== $final ? " → fusion {$final}" : ''),
    ];
}

try {
    $configDB = new ConfigDB($database);
    $pdoPublic = (new DBConnection($configDB->getEsquema($publicSchema)))->getPDO();
    $pdoPublic->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /** @var array<int, string> $idSchemaToName */
    $idSchemaToName = [];
    /** @var array<string, string> $basesLower */
    $basesLower = [];
    $stmtIds = $pdoPublic->query('SELECT id, schema FROM public.db_idschema');
    if ($stmtIds !== false) {
        while ($row = $stmtIds->fetch(PDO::FETCH_ASSOC)) {
            $name = (string) $row['schema'];
            $idSchemaToName[(int) $row['id']] = $name;
            $base = esquemaBase($name);
            $basesLower[strtolower($base)] = $base;
        }
    }

    // Bases de fusiones (pueden no existir ya en db_idschema; p. ej. H-dlv)
    foreach ($fusiones as $from => $to) {
        $basesLower[strtolower((string) $from)] = (string) $from;
        $basesLower[strtolower((string) $to)] = (string) $to;
    }

    /** @var array<string, true> $schemasConNotasDl (nsp reales o solo ids — para apply hace falta nsp) */
    $schemasConNotasDl = [];
    $stmtDl = $pdoPublic->query(
        "SELECT n.nspname FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
         WHERE c.relname = 'e_notas_dl' AND n.nspname NOT LIKE 'pg_%'"
    );
    if ($stmtDl !== false) {
        foreach ($stmtDl->fetchAll(PDO::FETCH_COLUMN) ?: [] as $s) {
            $schemasConNotasDl[(string) $s] = true;
        }
    }

    $stmtOr = $pdoPublic->query(
        "SELECT n.nspname FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
         WHERE c.relname = 'e_notas_otra_region_stgr' AND n.nspname NOT LIKE 'pg_%'
         ORDER BY 1"
    );
    $schemasOtraRegion = $stmtOr !== false ? ($stmtOr->fetchAll(PDO::FETCH_COLUMN) ?: []) : [];

    $tieneActividadesAll = false;
    try {
        $pdoPublic->query("SELECT 1 FROM {$publicSchema}.a_actividades_all LIMIT 0");
        $tieneActividadesAll = true;
    } catch (Throwable) {
        $tieneActividadesAll = false;
    }

    /** @var list<array<string, mixed>> $aRepatriar */
    $aRepatriar = [];
    /** @var list<array<string, mixed>> $aSinDestino */
    $aSinDestino = [];
    /** @var list<array<string, mixed>> $aPlaceholdersOtra */
    $aPlaceholdersOtra = [];
    /** @var list<array<string, mixed>> $aPlaceholdersDl */
    $aPlaceholdersDl = [];
    /** @var array<string, array{n:int, destino:?string, ejemplos:list<string>}> $statsPrefijo */
    $statsPrefijo = [];

    foreach ($schemasOtraRegion as $schema) {
        $schema = (string) $schema;
        try {
            $pdoSchema = (new DBConnection($configDB->getEsquema($schema)))->getPDO();
            $pdoSchema->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $e) {
            fwrite(STDERR, "AVISO: no se pudo conectar a {$schema}: {$e->getMessage()}\n");
            continue;
        }

        $sql = "SELECT id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                       preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta, id_schema
                FROM e_notas_otra_region_stgr
                ORDER BY id_nom, id_asignatura";
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        $rowsStmt = $pdoSchema->query($sql);
        if ($rowsStmt === false) {
            continue;
        }

        while ($row = $rowsStmt->fetch(PDO::FETCH_ASSOC)) {
            $idSituacion = (int) ($row['id_situacion'] ?? 0);
            $tipoActa = (int) ($row['tipo_acta'] ?? TipoActa::FORMATO_ACTA);
            $acta = (string) ($row['acta'] ?? '');
            $pref = $acta !== '' ? strtolower(trim(explode(' ', trim($acta), 2)[0])) : '(vacío)';

            $meta = [
                'origen' => $schema,
                'id_nom' => (int) $row['id_nom'],
                'id_nivel' => (int) $row['id_nivel'],
                'id_asignatura' => (int) $row['id_asignatura'],
                'tipo_acta' => $tipoActa,
                'id_situacion' => $idSituacion,
                'acta' => $acta,
                'id_activ' => isset($row['id_activ']) && $row['id_activ'] !== null ? (int) $row['id_activ'] : null,
                'id_schema' => isset($row['id_schema']) ? (int) $row['id_schema'] : null,
                'row' => $row,
            ];

            if ($idSituacion === NotaSituacion::FALTA_CERTIFICADO) {
                $aPlaceholdersOtra[] = $meta;
                continue;
            }

            $res = destinoDesdePrefijoActa($acta, $basesLower, $fusiones, $suffix);

            if ($res === null && $tieneActividadesAll && $meta['id_activ']) {
                try {
                    $st = $pdoPublic->prepare(
                        "SELECT dl_org FROM {$publicSchema}.a_actividades_all WHERE id_activ = :id LIMIT 1"
                    );
                    $st->execute(['id' => $meta['id_activ']]);
                    $dlOrg = $st->fetchColumn();
                    if (is_string($dlOrg) && $dlOrg !== '') {
                        $dl = strtolower(preg_replace('/f$/', '', $dlOrg) ?? $dlOrg);
                        if (isset($basesLower['h-' . $dl])) {
                            $final = aplicarFusion($basesLower['h-' . $dl] . $suffix, $fusiones, $suffix, $basesLower);
                            $res = [
                                'schema' => $final,
                                'via' => 'id_activ.dl_org',
                                'detail' => "id_activ={$meta['id_activ']} dl_org={$dlOrg} → {$final}",
                            ];
                        }
                    }
                } catch (Throwable) {
                    // ignore
                }
            }

            if ($res === null && $meta['id_schema'] !== null && isset($idSchemaToName[$meta['id_schema']])) {
                $name = $idSchemaToName[$meta['id_schema']];
                if (str_contains($name, '-') && !str_starts_with($name, 'resto') && !str_starts_with($name, 'public')) {
                    // Si es solo región STGR (H-Hv), no sirve como DL examinadora
                    $base = esquemaBase($name, $basesLower);
                    $esSoloRegion = (bool) preg_match('/^[A-Za-z]+-[A-Za-z]+$/', $base)
                        && !str_contains(strtolower($base), 'dl')
                        && !str_contains(strtolower($base), '-cr');
                    // H-Hv: region-dl pattern where second part equals first? H-H
                    if ($base === 'H-H' || preg_match('/^([A-Za-z]+)-\\1$/i', $base)) {
                        $esSoloRegion = true;
                    }
                    if (!$esSoloRegion) {
                        $final = aplicarFusion($base . $suffix, $fusiones, $suffix, $basesLower);
                        $res = [
                            'schema' => $final,
                            'via' => 'id_schema',
                            'detail' => "id_schema={$meta['id_schema']} → {$name} → {$final}",
                        ];
                    }
                }
            }

            $statsPrefijo[$pref] ??= ['n' => 0, 'destino' => null, 'ejemplos' => []];
            $statsPrefijo[$pref]['n']++;
            if ($res !== null) {
                $statsPrefijo[$pref]['destino'] = esquemaBase($res['schema'], $basesLower);
            }
            if (count($statsPrefijo[$pref]['ejemplos']) < 3 && $acta !== '') {
                $statsPrefijo[$pref]['ejemplos'][] = $acta;
            }

            if ($res === null) {
                $meta['via'] = 'none';
                $meta['detail'] = 'sin prefijo de acta mapeable (¿fusion faltante o acta especial?)';
                $meta['destino'] = null;
                $aSinDestino[] = $meta;
                continue;
            }

            $meta['via'] = $res['via'];
            $meta['detail'] = $res['detail'];
            $meta['destino'] = $res['schema'];

            // En dry-run reportamos aunque el nsp no exista en esta BD (p.ej. staging incompleto).
            if ($apply && !isset($schemasConNotasDl[$res['schema']])) {
                $meta['detail'] .= " (no existe {$res['schema']}.e_notas_dl en esta BD)";
                $aSinDestino[] = $meta;
                continue;
            }

            $aRepatriar[] = $meta;
        }
    }

    foreach (array_keys($schemasConNotasDl) as $schemaDl) {
        try {
            $pdoDl = (new DBConnection($configDB->getEsquema($schemaDl)))->getPDO();
            $phStmt = $pdoDl->query(
                "SELECT id_nom, id_nivel, id_asignatura FROM e_notas_dl
                 WHERE id_situacion = " . NotaSituacion::FALTA_CERTIFICADO . "
                   AND COALESCE(tipo_acta, " . TipoActa::FORMATO_ACTA . ") = " . TipoActa::FORMATO_CERTIFICADO
            );
            if ($phStmt === false) {
                continue;
            }
            while ($row = $phStmt->fetch(PDO::FETCH_ASSOC)) {
                $aPlaceholdersDl[] = [
                    'schema' => $schemaDl,
                    'id_nom' => (int) $row['id_nom'],
                    'id_nivel' => (int) $row['id_nivel'],
                    'id_asignatura' => (int) $row['id_asignatura'],
                ];
            }
        } catch (Throwable) {
            continue;
        }
    }

    $moved = 0;
    $skippedDup = 0;
    $deletedPh = 0;

    if ($apply) {
        // Apply usa conexión del esquema destino / origen
        foreach ($aRepatriar as $meta) {
            $destino = (string) $meta['destino'];
            $origen = (string) $meta['origen'];
            $pdoDst = (new DBConnection($configDB->getEsquema($destino)))->getPDO();
            $pdoOrg = (new DBConnection($configDB->getEsquema($origen)))->getPDO();
            $row = $meta['row'];

            $exists = $pdoDst->prepare(
                'SELECT 1 FROM e_notas_dl WHERE id_nom = :id_nom AND id_asignatura = :id_asignatura LIMIT 1'
            );
            $exists->execute(['id_nom' => $meta['id_nom'], 'id_asignatura' => $meta['id_asignatura']]);
            if ($exists->fetchColumn() !== false) {
                $skippedDup++;
                $del = $pdoOrg->prepare(
                    'DELETE FROM e_notas_otra_region_stgr
                     WHERE id_nom = :id_nom AND id_nivel = :id_nivel AND tipo_acta = :tipo_acta'
                );
                $del->execute([
                    'id_nom' => $meta['id_nom'],
                    'id_nivel' => $meta['id_nivel'],
                    'tipo_acta' => $meta['tipo_acta'],
                ]);
                continue;
            }

            $ins = $pdoDst->prepare(
                'INSERT INTO e_notas_dl
                    (id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle,
                     preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta)
                 VALUES
                    (:id_nom, :id_nivel, :id_asignatura, :id_situacion, :acta, :f_acta, :detalle,
                     :preceptor, :id_preceptor, :epoca, :id_activ, :nota_num, :nota_max, :tipo_acta)'
            );
            $ins->execute([
                'id_nom' => $row['id_nom'],
                'id_nivel' => $row['id_nivel'],
                'id_asignatura' => $row['id_asignatura'],
                'id_situacion' => $row['id_situacion'],
                'acta' => $row['acta'],
                'f_acta' => $row['f_acta'],
                'detalle' => $row['detalle'],
                'preceptor' => $row['preceptor'],
                'id_preceptor' => $row['id_preceptor'],
                'epoca' => $row['epoca'],
                'id_activ' => $row['id_activ'],
                'nota_num' => $row['nota_num'],
                'nota_max' => $row['nota_max'],
                'tipo_acta' => $row['tipo_acta'] ?? TipoActa::FORMATO_ACTA,
            ]);
            $del = $pdoOrg->prepare(
                'DELETE FROM e_notas_otra_region_stgr
                 WHERE id_nom = :id_nom AND id_nivel = :id_nivel AND tipo_acta = :tipo_acta'
            );
            $del->execute([
                'id_nom' => $meta['id_nom'],
                'id_nivel' => $meta['id_nivel'],
                'tipo_acta' => $meta['tipo_acta'],
            ]);
            $moved++;
        }

        foreach ($aPlaceholdersOtra as $meta) {
            $pdoOrg = (new DBConnection($configDB->getEsquema((string) $meta['origen'])))->getPDO();
            $del = $pdoOrg->prepare(
                'DELETE FROM e_notas_otra_region_stgr
                 WHERE id_nom = :id_nom AND id_nivel = :id_nivel AND tipo_acta = :tipo_acta
                   AND id_situacion = ' . NotaSituacion::FALTA_CERTIFICADO
            );
            $del->execute([
                'id_nom' => $meta['id_nom'],
                'id_nivel' => $meta['id_nivel'],
                'tipo_acta' => $meta['tipo_acta'],
            ]);
            $deletedPh += $del->rowCount();
        }
        foreach ($aPlaceholdersDl as $ph) {
            $pdoDl = (new DBConnection($configDB->getEsquema($ph['schema'])))->getPDO();
            $del = $pdoDl->prepare(
                'DELETE FROM e_notas_dl
                 WHERE id_nom = :id_nom AND id_nivel = :id_nivel AND id_asignatura = :id_asignatura
                   AND id_situacion = ' . NotaSituacion::FALTA_CERTIFICADO . '
                   AND COALESCE(tipo_acta, ' . TipoActa::FORMATO_ACTA . ') = ' . TipoActa::FORMATO_CERTIFICADO
            );
            $del->execute([
                'id_nom' => $ph['id_nom'],
                'id_nivel' => $ph['id_nivel'],
                'id_asignatura' => $ph['id_asignatura'],
            ]);
            $deletedPh += $del->rowCount();
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: {$e->getMessage()}\n");
    exit(1);
}

$mode = $apply ? 'APPLY' : 'DRY-RUN';
echo "fix_notas_otra_region_a_acta — {$mode} — database={$database}\n";
$pares = [];
foreach ($fusiones as $k => $v) {
    $pares[] = "{$k}→{$v}";
}
echo 'Fusiones: ' . ( $pares === [] ? '(ninguna)' : implode(', ', $pares) ) . "\n\n";

if ($porPrefijo || !$apply) {
    echo "Resumen por prefijo de acta (H-Hv y demás otra_region):\n";
    uasort($statsPrefijo, static fn($a, $b) => $b['n'] <=> $a['n']);
    foreach ($statsPrefijo as $pref => $st) {
        $dest = $st['destino'] ?? '???';
        $ej = $st['ejemplos'] !== [] ? '  ej: ' . implode(', ', $st['ejemplos']) : '';
        echo sprintf("  %-12s  n=%-5d  → %-20s%s\n", $pref, $st['n'], $dest, $ej);
    }
    echo "\n";
}

echo 'A repatriar: ' . count($aRepatriar) . "\n";
echo 'Sin destino: ' . count($aSinDestino) . "\n";
if ($aSinDestino !== []) {
    echo "Detalle sin destino (muestra):\n";
    foreach (array_slice($aSinDestino, 0, 25) as $m) {
        echo sprintf(
            "  origen=%s id_nom=%d acta=%s\n",
            $m['origen'],
            $m['id_nom'],
            $m['acta'] !== '' ? $m['acta'] : '(vacío)'
        );
    }
}

echo 'Placeholders: otra_region=' . count($aPlaceholdersOtra) . ' dl=' . count($aPlaceholdersDl) . "\n";

if ($apply) {
    echo "\nResultado: repatriadas={$moved} dups_origen_borrado={$skippedDup} placeholders_borrados={$deletedPh}\n";
    exit($aSinDestino !== [] ? 2 : 0);
}

echo "\nDry-run OK. Aplicar vía migraciones web:\n";
echo "  db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sv.sql\n";
echo "  db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sf.sql\n";
exit(0);
