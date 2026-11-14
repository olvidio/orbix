<?php

declare(strict_types=1);

/**
 * Ejecuta PHPUnit en el contenedor y comprueba que ninguna tabla de las BD de desarrollo ha perdido filas.
 *
 * Por qué: los tests de integración crean filas, las guardan y las borran. Si el id que usan ya existía
 * (secuencia por detrás de los ids reales, o id fijo), `Guardar` sobrescribe la fila real y el borrado
 * se la lleva. Así se perdieron en noviembre de 2026 diseños de procesos, usuarios, preferencias y
 * avisos de la BD de desarrollo sin que ningún test fallara.
 *
 * Qué hace: cuenta las filas de todas las tablas de `comun`, `sv`, `sv-e` y `sf` antes y después de
 * PHPUnit. Si alguna tabla tiene menos filas al acabar, lo lista y termina con código 1 (aunque los
 * tests hayan pasado). Antes de empezar comprueba las secuencias de `comun` y `sv-e` (réplicas: al
 * restaurarlas quedan sin ajustar); si alguna va por detrás del máximo de su tabla, no ejecuta PHPUnit
 * (código 2).
 *
 * No detecta una fila real sobrescrita que no se llegue a borrar.
 *
 * Uso (desde la raíz del repo, en el host; los argumentos son los de PHPUnit):
 *   php tools/qa/phpunit_protegido.php tests/integration/inventario
 *   php tools/qa/phpunit_protegido.php                       # toda la suite
 *
 * Variables de entorno: ORBIX_DB_CONTAINER (orbix-db-1), ORBIX_PHP_CONTAINER (orbix-php-fpm-1),
 * ORBIX_DBS ("comun sv sv-e sf"), PHPUNIT_CONFIG (build/phpunit-no-stop.xml).
 */

$dbContainer = getenv('ORBIX_DB_CONTAINER') ?: 'orbix-db-1';
$phpContainer = getenv('ORBIX_PHP_CONTAINER') ?: 'orbix-php-fpm-1';
$dbs = preg_split('/\s+/', trim(getenv('ORBIX_DBS') ?: 'comun sv sv-e sf')) ?: [];
$config = getenv('PHPUNIT_CONFIG') ?: 'build/phpunit-no-stop.xml';

const SQL_RECUENTO = <<<'SQL'
CREATE TEMP TABLE zz_recuento (t text, n bigint);
DO $$ DECLARE r record; c bigint; BEGIN
  FOR r IN SELECT n.nspname s, c.relname t FROM pg_class c JOIN pg_namespace n ON n.oid = c.relnamespace
           WHERE c.relkind = 'r' AND n.nspname NOT IN ('pg_catalog', 'information_schema') AND n.nspname NOT LIKE 'pg_%' LOOP
    EXECUTE format('SELECT count(*) FROM ONLY %I.%I', r.s, r.t) INTO c;
    INSERT INTO zz_recuento VALUES (format('%I.%I', r.s, r.t), c);
  END LOOP; END $$;
SELECT t, n FROM zz_recuento ORDER BY 1;
SQL;

const SQL_SECUENCIAS = <<<'SQL'
CREATE TEMP TABLE zz_seq (tabla text, last_value bigint, max_id bigint);
DO $$ DECLARE r record; m bigint; lv bigint; BEGIN
  FOR r IN SELECT n.nspname s, c.relname t, a.attname col,
                  (regexp_match(pg_get_expr(d.adbin, d.adrelid), 'nextval\(''([^'']+)''')) [1] AS seq
           FROM pg_attrdef d JOIN pg_class c ON c.oid = d.adrelid AND c.relkind = 'r'
           JOIN pg_namespace n ON n.oid = c.relnamespace
           JOIN pg_attribute a ON a.attrelid = d.adrelid AND a.attnum = d.adnum
           WHERE pg_get_expr(d.adbin, d.adrelid) LIKE 'nextval(%' AND n.nspname NOT IN ('pg_catalog', 'information_schema') LOOP
    BEGIN
      -- Los ids >= 9000 de xa_asignaturas son especiales (9998, 9999): no cuentan como máximo.
      IF r.t = 'xa_asignaturas' THEN
        EXECUTE format('SELECT max(%I) FROM ONLY %I.%I WHERE %I < 9000', r.col, r.s, r.t, r.col) INTO m;
      ELSE
        EXECUTE format('SELECT max(%I) FROM ONLY %I.%I', r.col, r.s, r.t) INTO m;
      END IF;
      EXECUTE format('SELECT last_value FROM %s', r.seq) INTO lv;
      INSERT INTO zz_seq VALUES (format('%I.%I', r.s, r.t), lv, m);
    EXCEPTION WHEN OTHERS THEN NULL;
    END;
  END LOOP; END $$;
SELECT tabla, last_value, max_id FROM zz_seq WHERE max_id > last_value ORDER BY 1;
SQL;

/**
 * Ejecuta SQL con el superusuario de la BD del contenedor y devuelve las filas (campos separados por tabulador).
 *
 * @return list<list<string>>|null null si falla
 */
function psql(string $contenedor, string $db, string $sql): ?array
{
    $cmd = ['docker', 'exec', '-i', $contenedor, 'psql', '-U', 'postgres', '-d', $db, '-At', '-F', "\t", '-q'];
    $proc = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($proc)) {
        return null;
    }
    fwrite($pipes[0], $sql);
    fclose($pipes[0]);
    $out = (string) stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    if (proc_close($proc) !== 0) {
        return null;
    }
    $filas = [];
    foreach (explode("\n", trim($out)) as $linea) {
        if ($linea !== '') {
            $filas[] = explode("\t", $linea);
        }
    }

    return $filas;
}

/**
 * @param list<string> $dbs
 * @return array<string, array<string, int>> bd => tabla => filas
 */
function recuento(string $contenedor, array $dbs): array
{
    $r = [];
    foreach ($dbs as $db) {
        $filas = psql($contenedor, $db, SQL_RECUENTO);
        if ($filas === null || $filas === []) {
            fwrite(STDERR, "No se pueden contar las filas de $db\n");
            exit(2);
        }
        foreach ($filas as $f) {
            $r[$db][$f[0]] = (int) ($f[1] ?? 0);
        }
    }

    return $r;
}

echo "== Secuencias por detrás del máximo de su tabla (comun, sv-e) ==\n";
$atrasadas = false;
foreach (array_intersect(['comun', 'sv-e'], $dbs) as $db) {
    foreach (psql($dbContainer, $db, SQL_SECUENCIAS) ?? [] as $f) {
        $atrasadas = true;
        printf("  %s  %s: secuencia %s, máximo %s\n", $db, $f[0], $f[1] ?? '', $f[2] ?? '');
    }
}
if ($atrasadas) {
    fwrite(STDERR, "Hay secuencias atrasadas: un alta de un test puede sobrescribir una fila real.\n"
        . "Ajústalas (setval al máximo de su tabla) y vuelve a lanzar. No se ejecuta PHPUnit.\n");
    exit(2);
}
echo "  ninguna\n";

echo "== Contando filas antes de PHPUnit ==\n";
$antes = recuento($dbContainer, $dbs);

$cmd = array_merge(
    ['docker', 'exec', '-w', '/var/www/orbix', $phpContainer, 'php', '-d', 'xdebug.mode=off', '-d', 'memory_limit=2G', 'libs/vendor/bin/phpunit', '-c', $config],
    array_slice($argv, 1),
);
passthru(implode(' ', array_map('escapeshellarg', $cmd)), $rc);

echo "== Contando filas después de PHPUnit ==\n";
$despues = recuento($dbContainer, $dbs);

/*
 * Tablas que la propia aplicación purga cuando los tests ejecutan de verdad sus tareas de mantenimiento
 * (no es un test pisando datos): se avisa, pero no cuenta como fallo.
 *  - av_cambios*: el generador de avisos borra los cambios de más de un año (`borrarCambios('P1Y')`).
 *  - a_actividad_proceso_*: «nuevo curso» borra los procesos de actividades que no existen (en dev, las
 *    de delegaciones que no están cargadas; solo ocurre la primera vez tras restaurar la BD).
 *  - tmp_notas_*: tablas de trabajo de los resúmenes de notas, que la aplicación vacía y vuelve a llenar.
 */
const PURGAS_DE_LA_APLICACION = '/\.(av_cambios|av_cambios_dl|av_cambios_anotados_dl|av_cambios_anotados_dl_sf|a_actividad_proceso_sv|a_actividad_proceso_sf|tmp_notas_\w+)$/';

$perdidas = false;
foreach ($antes as $db => $tablas) {
    foreach ($tablas as $tabla => $n) {
        $ahora = $despues[$db][$tabla] ?? 0;
        if ($ahora < $n && preg_match(PURGAS_DE_LA_APLICACION, $tabla) === 1) {
            printf("  aviso: [%s] %s tiene %d filas menos (purga de la propia aplicación)\n", $db, $tabla, $n - $ahora);
            continue;
        }
        if ($ahora < $n) {
            if (!$perdidas) {
                fwrite(STDERR, "TABLAS QUE HAN PERDIDO FILAS DURANTE LOS TESTS:\n");
                $perdidas = true;
            }
            fwrite(STDERR, sprintf("  [%s] %-66s antes %9d  después %9d  faltan %d\n", $db, $tabla, $n, $ahora, $n - $ahora));
        }
    }
}
if ($perdidas) {
    fwrite(STDERR, "Algún test ha borrado filas que no creó. Hay que reponerlas (copias en ~/trasvases) y corregir el test.\n");
    exit(1);
}
echo "Ninguna tabla ha perdido filas.\n";
exit($rc);
