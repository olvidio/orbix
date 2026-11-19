<?php

declare(strict_types=1);

// Script de línea de órdenes: no hace nada si se pide por el servidor web.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

/**
 * BD de pruebas: clones de las BD de desarrollo (`pruebas-comun`, `pruebas-sv`, `pruebas-sv-e`,
 * `pruebas-sf`) contra los que corren PHPUnit y Playwright, para que no escriban en las de desarrollo.
 *
 * Los clones se hacen en el mismo servidor PostgreSQL con `CREATE DATABASE … TEMPLATE …` (copia de
 * ficheros: unos 30 s para los 1,9 GB). PostgreSQL exige que no haya nadie conectado a la BD de origen
 * mientras se clona: si lo hay, el script lo dice y no hace nada.
 *
 * La aplicación usa los clones cuando `WEBDIR=pruebas` (lee `/var/www/conf/pruebas-*.conn.inc`):
 * `tools/qa/phpunit_protegido.php` lanza así PHPUnit, y la instancia web de pruebas (nginx
 * `orbix-pruebas.conf`, http://orbix.local:8003/orbix) lo lleva en su `fastcgi_param`.
 * Montaje completo: `docs/dev/bd_pruebas.md`.
 *
 * Uso (desde la raíz del repo, en el host):
 *   php tools/qa/bd_pruebas.php estado       # qué clones hay, de cuándo y si conviene regenerarlos
 *   php tools/qa/bd_pruebas.php asegurar     # crea los que falten y regenera si hace falta (lo llama el envoltorio)
 *   php tools/qa/bd_pruebas.php regenerar    # borra los clones y los vuelve a crear
 *   php tools/qa/bd_pruebas.php marcar-danada "motivo"   # la próxima vez, `asegurar` regenera
 *   php tools/qa/bd_pruebas.php borrar       # quita los clones
 *
 * Cuándo regenera `asegurar`:
 *  - falta algún clon;
 *  - el esquema de la BD de desarrollo ha cambiado desde que se clonó (tablas, columnas o tipos:
 *    una migración aplicada en desarrollo);
 *  - una pasada dejó el clon marcado como dañado (tablas que perdieron filas);
 *  - el clon tiene más de ORBIX_PRUEBAS_MAX_DIAS días (por defecto 15).
 * Si toca regenerar y hay alguien conectado a desarrollo, sigue con los clones que hay y avisa.
 *
 * Tras clonar ajusta las secuencias atrasadas de `pruebas-comun` y `pruebas-sv-e` (en desarrollo son
 * copias de réplicas y vienen sin ajustar). Las de `pruebas-sv` no se tocan: si alguna va por detrás,
 * hay que mirar qué pasa en producción.
 *
 * Variables de entorno: ORBIX_DB_CONTAINER (orbix-db-1), ORBIX_DBS ("comun sv sv-e sf"),
 * ORBIX_PRUEBAS_MAX_DIAS (15).
 */

$contenedor = getenv('ORBIX_DB_CONTAINER') ?: 'orbix-db-1';
$bases = preg_split('/\s+/', trim(getenv('ORBIX_DBS') ?: 'comun sv sv-e sf')) ?: [];
$maxDias = (int) (getenv('ORBIX_PRUEBAS_MAX_DIAS') ?: 15);
$orden = $argv[1] ?? 'estado';

const PREFIJO = 'pruebas-';

/** Huella del esquema: tablas, columnas y tipos de los esquemas de la aplicación. */
const SQL_HUELLA_ESQUEMA = <<<'SQL'
SELECT md5(coalesce(string_agg(table_schema || '.' || table_name || '.' || column_name || ':' || data_type || ':' || coalesce(character_maximum_length::text, ''), ',' ORDER BY table_schema, table_name, column_name), ''))
FROM information_schema.columns
WHERE table_schema NOT IN ('pg_catalog', 'information_schema') AND table_schema NOT LIKE 'pg_%';
SQL;

/** Pone al máximo de su tabla las secuencias que van por detrás (misma detección que el envoltorio). */
const SQL_AJUSTAR_SECUENCIAS = <<<'SQL'
DO $$ DECLARE r record; m bigint; lv bigint; BEGIN
  FOR r IN SELECT n.nspname s, c.relname t, a.attname col,
                  (regexp_match(pg_get_expr(d.adbin, d.adrelid), 'nextval\(''([^'']+)''')) [1] AS seq
           FROM pg_attrdef d JOIN pg_class c ON c.oid = d.adrelid AND c.relkind = 'r'
           JOIN pg_namespace n ON n.oid = c.relnamespace
           JOIN pg_attribute a ON a.attrelid = d.adrelid AND a.attnum = d.adnum
           WHERE pg_get_expr(d.adbin, d.adrelid) LIKE 'nextval(%' AND n.nspname NOT IN ('pg_catalog', 'information_schema') LOOP
    BEGIN
      IF r.t = 'xa_asignaturas' THEN
        EXECUTE format('SELECT max(%I) FROM ONLY %I.%I WHERE %I < 9000', r.col, r.s, r.t, r.col) INTO m;
      ELSE
        EXECUTE format('SELECT max(%I) FROM ONLY %I.%I', r.col, r.s, r.t) INTO m;
      END IF;
      EXECUTE format('SELECT last_value FROM %s', r.seq) INTO lv;
      IF m IS NOT NULL AND lv < m THEN
        PERFORM setval(r.seq::regclass, m);
        RAISE NOTICE 'secuencia % de % a %', r.seq, lv, m;
      END IF;
    EXCEPTION WHEN OTHERS THEN NULL;
    END;
  END LOOP; END $$;
SQL;

/**
 * @return array{0: int, 1: string} código de salida y salida (stdout + stderr)
 */
function psql(string $contenedor, string $db, string $sql): array
{
    $cmd = ['docker', 'exec', '-i', $contenedor, 'psql', '-U', 'postgres', '-d', $db, '-At', '-q', '-v', 'ON_ERROR_STOP=1'];
    $proc = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($proc)) {
        return [1, 'no se puede lanzar psql'];
    }
    fwrite($pipes[0], $sql);
    fclose($pipes[0]);
    $out = trim((string) stream_get_contents($pipes[1]) . "\n" . (string) stream_get_contents($pipes[2]));
    fclose($pipes[1]);
    fclose($pipes[2]);

    return [proc_close($proc), $out];
}

function literal(string $texto): string
{
    return "'" . str_replace("'", "''", $texto) . "'";
}

/**
 * Lo que se sabe de un clon: está en el comentario de la BD (JSON).
 *
 * @return array{existe: bool, creada: string, esquema: string, danada: string}
 */
function infoClon(string $contenedor, string $base): array
{
    [$rc, $out] = psql($contenedor, 'postgres', 'SELECT coalesce(shobj_description(oid, \'pg_database\'), \'\') FROM pg_database WHERE datname = ' . literal(PREFIJO . $base));
    $info = ['existe' => $rc === 0 && $out !== '' || ($rc === 0 && existe($contenedor, PREFIJO . $base)), 'creada' => '', 'esquema' => '', 'danada' => ''];
    $datos = json_decode($out, true);
    if (is_array($datos)) {
        foreach (['creada', 'esquema', 'danada'] as $clave) {
            $info[$clave] = is_string($datos[$clave] ?? null) ? $datos[$clave] : '';
        }
    }

    return $info;
}

function existe(string $contenedor, string $bd): bool
{
    [$rc, $out] = psql($contenedor, 'postgres', 'SELECT 1 FROM pg_database WHERE datname = ' . literal($bd));

    return $rc === 0 && trim($out) === '1';
}

/** @param array{creada: string, esquema: string, danada: string} $info */
function anotar(string $contenedor, string $base, array $info): void
{
    psql($contenedor, 'postgres', sprintf('COMMENT ON DATABASE "%s" IS %s', PREFIJO . $base, literal((string) json_encode($info))));
}

function huellaEsquema(string $contenedor, string $bd): string
{
    [$rc, $out] = psql($contenedor, $bd, SQL_HUELLA_ESQUEMA);

    return $rc === 0 ? trim($out) : '';
}

/** Quién está conectado a una BD (sin contar esta consulta). */
function conectados(string $contenedor, string $bd): string
{
    [, $out] = psql($contenedor, 'postgres', 'SELECT string_agg(DISTINCT coalesce(usename, \'?\') || \'@\' || coalesce(client_addr::text, \'local\') || \' (\' || state || \')\', \', \') FROM pg_stat_activity WHERE datname = ' . literal($bd) . ' AND pid <> pg_backend_pid()');

    return trim($out);
}

/**
 * Motivo por el que conviene regenerar el clon de `$base`; '' si está bien.
 */
function motivoParaRegenerar(string $contenedor, string $base, int $maxDias): string
{
    $info = infoClon($contenedor, $base);
    if (!$info['existe']) {
        return 'no existe';
    }
    if ($info['danada'] !== '') {
        return 'una pasada lo dejó dañado: ' . $info['danada'];
    }
    if ($info['esquema'] !== '' && $info['esquema'] !== huellaEsquema($contenedor, $base)) {
        return 'el esquema de desarrollo ha cambiado desde que se clonó';
    }
    $creada = $info['creada'] === '' ? false : strtotime($info['creada']);
    if ($creada === false) {
        return 'no se sabe cuándo se clonó';
    }
    $dias = (int) floor((time() - $creada) / 86400);

    return $dias > $maxDias ? "tiene $dias días (máximo $maxDias)" : '';
}

/**
 * Borra el clon de `$base` (si existe) y lo vuelve a crear. Devuelve '' o el error.
 */
function clonar(string $contenedor, string $base): string
{
    $clon = PREFIJO . $base;
    $quien = conectados($contenedor, $base);
    if ($quien !== '') {
        return "hay conexiones abiertas a «{$base}»: $quien. PostgreSQL no deja clonar una BD en uso; cierra esas sesiones (la aplicación de desarrollo las suelta sola al acabar cada petición) y vuelve a lanzarlo";
    }
    $t0 = microtime(true);
    psql($contenedor, 'postgres', 'SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = ' . literal($clon) . ' AND pid <> pg_backend_pid()');
    [$rc, $out] = psql($contenedor, 'postgres', sprintf('DROP DATABASE IF EXISTS "%s"', $clon));
    if ($rc !== 0) {
        return "no se puede borrar «{$clon}»: $out";
    }
    [$rc, $out] = psql($contenedor, 'postgres', sprintf('CREATE DATABASE "%s" TEMPLATE "%s"', $clon, $base));
    if ($rc !== 0) {
        return "no se puede clonar «{$base}»: $out";
    }
    $secuencias = '';
    if (in_array($base, ['comun', 'sv-e'], true)) {
        [, $avisos] = psql($contenedor, $clon, SQL_AJUSTAR_SECUENCIAS);
        $n = substr_count($avisos, 'NOTICE');
        $secuencias = $n > 0 ? "; $n secuencias ajustadas" : '';
    }
    anotar($contenedor, $base, ['creada' => date('c'), 'esquema' => huellaEsquema($contenedor, $base), 'danada' => '']);
    printf("  %-14s clonada de %s en %.0f s%s\n", $clon, $base, microtime(true) - $t0, $secuencias);

    return '';
}

switch ($orden) {
    case 'estado':
        foreach ($bases as $base) {
            $info = infoClon($contenedor, $base);
            $motivo = motivoParaRegenerar($contenedor, $base, $maxDias);
            printf("%-14s %s%s\n", PREFIJO . $base, $info['existe'] ? 'clonada el ' . ($info['creada'] ?: '?') : 'no existe', $motivo === '' ? '' : "  → conviene regenerar: $motivo");
        }
        exit(0);

    case 'asegurar':
        $rc = 0;
        foreach ($bases as $base) {
            $motivo = motivoParaRegenerar($contenedor, $base, $maxDias);
            if ($motivo === '') {
                continue;
            }
            echo "  " . PREFIJO . "$base: $motivo; se regenera\n";
            $error = clonar($contenedor, $base);
            if ($error === '') {
                continue;
            }
            if (existe($contenedor, PREFIJO . $base)) {
                // Se puede seguir con el clon que hay; la próxima pasada lo vuelve a intentar.
                echo "  aviso: no se ha regenerado ($error). Se sigue con el clon actual.\n";
            } else {
                fwrite(STDERR, "  $error\n");
                $rc = 2;
            }
        }
        exit($rc);

    case 'regenerar':
        $rc = 0;
        foreach ($bases as $base) {
            $error = clonar($contenedor, $base);
            if ($error !== '') {
                fwrite(STDERR, "  $error\n");
                $rc = 2;
            }
        }
        exit($rc);

    case 'marcar-danada':
        $motivo = $argv[2] ?? 'sin motivo';
        foreach ($bases as $base) {
            $info = infoClon($contenedor, $base);
            if ($info['existe']) {
                anotar($contenedor, $base, ['creada' => $info['creada'], 'esquema' => $info['esquema'], 'danada' => $motivo]);
            }
        }
        exit(0);

    case 'borrar':
        foreach ($bases as $base) {
            psql($contenedor, 'postgres', 'SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = ' . literal(PREFIJO . $base) . ' AND pid <> pg_backend_pid()');
            [$rc, $out] = psql($contenedor, 'postgres', sprintf('DROP DATABASE IF EXISTS "%s"', PREFIJO . $base));
            echo $rc === 0 ? '  ' . PREFIJO . "$base borrada\n" : "  $out\n";
        }
        exit(0);

    default:
        fwrite(STDERR, "Uso: php tools/qa/bd_pruebas.php estado|asegurar|regenerar|marcar-danada \"motivo\"|borrar\n");
        exit(2);
}
