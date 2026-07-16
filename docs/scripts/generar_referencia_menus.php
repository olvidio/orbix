<?php

/**
 * Genera la referencia de menús de Orbix a partir de la base de datos actual.
 *
 * Fuente de verdad (no el antiguo `docs/legacy/obix/menus.csv`):
 *   - Metamenús (destinos URL, IGUALES para todos): base `comun`, esquema `public`,
 *     tabla `aux_metamenus` + catálogo de módulos `m0_modulos`.
 *   - Árbol de menús POR LAYOUT: base `sv-e`, un esquema por delegación/layout,
 *     tablas `aux_menus` (columna `orden` = ruta jerárquica) + `aux_grupmenu`.
 *
 * Para la documentación se toman 2 layouts de ejemplo (configurable abajo):
 *   - Legacy  -> esquema `H-dlbv`
 *   - Pills2  -> esquema `H-dlpv`
 *
 * Salida: `docs/guias/_referencia_menus.md`, con:
 *   1. Tabla de metamenús (URL + módulo).
 *   2. Árbol completo de cada layout (por grupo de menú).
 *   3. Índice «URL + parámetros -> ruta Legacy / ruta Pills2» (lo que usan las guías).
 *
 * Conexión configurable por entorno (por defecto, contenedor docker expuesto en host):
 *   ORBIX_DB_HOST (localhost)  ORBIX_DB_PORT (5444)
 *   ORBIX_DB_USER (postgres)   ORBIX_DB_PASS (postgres)
 *
 * Uso:
 *   php docs/scripts/generar_referencia_menus.php [--dry-run]
 */

declare(strict_types=1);

// ------------------------------------------------------------------ config ---

$DB_HOST = getenv('ORBIX_DB_HOST') ?: 'localhost';
$DB_PORT = getenv('ORBIX_DB_PORT') ?: '5444';
$DB_USER = getenv('ORBIX_DB_USER') ?: 'postgres';
$DB_PASS = getenv('ORBIX_DB_PASS');
if ($DB_PASS === false) {
    $DB_PASS = 'postgres';
}

$DB_METAMENUS = 'comun';   // base con aux_metamenus + m0_modulos (esquema public)
$DB_MENUS     = 'sv-e';    // base con los árboles de menús por esquema

/** Layouts a documentar: etiqueta => esquema en la base $DB_MENUS. */
$LAYOUTS = [
    'Legacy' => 'H-dlbv',
    'Pills2' => 'H-dlpv',
];

$OUTPUT = dirname(__DIR__) . '/guias/_referencia_menus.md';

$dryRun = in_array('--dry-run', $argv, true);

// --------------------------------------------------------------- utilidades ---

/** @return array<int,int> parsea un literal de array postgres `{15,10}` a lista de int. */
function parseIntArray(?string $literal): array
{
    if ($literal === null || $literal === '' || $literal === '{}') {
        return [];
    }
    $inner = trim($literal, '{}');
    if ($inner === '') {
        return [];
    }
    return array_map('intval', explode(',', $inner));
}

/**
 * Compara dos rutas `orden` lexicográficamente (elemento a elemento; el prefijo más
 * corto va antes). No usar `<=>` de PHP directamente: para arrays compara primero por
 * número de elementos, lo que rompería el anidamiento del árbol.
 *
 * @param array<int,int> $a
 * @param array<int,int> $b
 */
function cmpOrden(array $a, array $b): int
{
    $n = min(count($a), count($b));
    for ($i = 0; $i < $n; $i++) {
        if ($a[$i] !== $b[$i]) {
            return $a[$i] <=> $b[$i];
        }
    }
    return count($a) <=> count($b);
}

function pdoConnect(string $host, string $port, string $db, string $user, string $pass): PDO
{
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $db);
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

/** Limpia la etiqueta de un grupo de menú (Pills2 la guarda como `|PERSONAS|`). */
function limpiaGrupo(string $g): string
{
    return trim(str_replace('|', '', $g));
}

// --------------------------------------------------------- carga metamenús ---

echo "Conectando a {$DB_METAMENUS}@{$DB_HOST}:{$DB_PORT}...\n";
$pdoComun = pdoConnect($DB_HOST, $DB_PORT, $DB_METAMENUS, $DB_USER, $DB_PASS);

/** @var array<int,array{nom:string}> $modulos */
$modulos = [];
foreach ($pdoComun->query('SELECT id_mod, nom FROM public.m0_modulos') as $row) {
    $modulos[(int) $row['id_mod']] = $row['nom'];
}

/** @var array<int,array{id_mod:?int,url:?string,parametros:?string,descripcion:?string}> $metamenus */
$metamenus = [];
$sql = 'SELECT id_metamenu, id_mod, url, parametros, descripcion FROM public.aux_metamenus ORDER BY id_metamenu';
foreach ($pdoComun->query($sql) as $row) {
    $metamenus[(int) $row['id_metamenu']] = [
        'id_mod'      => $row['id_mod'] !== null ? (int) $row['id_mod'] : null,
        'url'         => $row['url'],
        'parametros'  => $row['parametros'],
        'descripcion' => $row['descripcion'],
    ];
}
echo '  metamenús: ' . count($metamenus) . ', módulos: ' . count($modulos) . "\n";

// ------------------------------------------------- carga árboles por layout ---

echo "Conectando a {$DB_MENUS}@{$DB_HOST}:{$DB_PORT}...\n";
$pdoMenus = pdoConnect($DB_HOST, $DB_PORT, $DB_MENUS, $DB_USER, $DB_PASS);

/**
 * @var array<string,array{
 *   grupos: array<int,array{nom:string,orden:?int}>,
 *   labels: array<string,string>,
 *   leaves: list<array{id_grupmenu:int,orden:array<int,int>,menu:string,id_metamenu:?int}>
 * }> $trees
 */
$trees = [];

foreach ($LAYOUTS as $etiqueta => $esquema) {
    echo "  layout {$etiqueta} (esquema {$esquema})...\n";
    $pdoMenus->exec('SET search_path TO "' . $esquema . '"');

    $grupos = [];
    $sqlG = 'SELECT id_grupmenu, grup_menu, orden FROM aux_grupmenu';
    foreach ($pdoMenus->query($sqlG) as $row) {
        $grupos[(int) $row['id_grupmenu']] = [
            'nom'   => (string) $row['grup_menu'],
            'orden' => $row['orden'] !== null ? (int) $row['orden'] : null,
        ];
    }

    $labels = [];  // "id_grupmenu|15,10" => etiqueta
    $rows = [];
    $sqlM = 'SELECT id_grupmenu, orden, menu, parametros, id_metamenu, ok FROM aux_menus';
    foreach ($pdoMenus->query($sqlM) as $row) {
        $orden = parseIntArray($row['orden']);
        if ($orden === []) {
            continue;
        }
        $idG = (int) $row['id_grupmenu'];
        $labels[$idG . '|' . implode(',', $orden)] = (string) $row['menu'];
        $rows[] = [
            'id_grupmenu' => $idG,
            'orden'       => $orden,
            'menu'        => (string) $row['menu'],
            'parametros'  => $row['parametros'],
            'id_metamenu' => $row['id_metamenu'] !== null ? (int) $row['id_metamenu'] : null,
            'ok'          => ($row['ok'] === true || $row['ok'] === 't'),
        ];
    }

    $trees[$etiqueta] = ['grupos' => $grupos, 'labels' => $labels, 'rows' => $rows];
}

// ---------------------------------------------------- construcción de rutas ---

/**
 * Ruta completa de una entrada: `grupo > nivel1 > ... > hoja`.
 * @param array<int,int> $orden
 * @param array<string,string> $labels
 */
function construyeRuta(string $grupoNom, int $idG, array $orden, array $labels): string
{
    $parts = [limpiaGrupo($grupoNom)];
    $prefix = [];
    foreach ($orden as $n) {
        $prefix[] = $n;
        $key = $idG . '|' . implode(',', $prefix);
        $parts[] = $labels[$key] ?? ('?' . $n);
    }
    return implode(' > ', $parts);
}

// Índice: "url\nparams" => ['Legacy'=>[rutas], 'Pills2'=>[rutas], meta...]
$indice = [];

foreach ($trees as $etiqueta => $tree) {
    foreach ($tree['rows'] as $r) {
        $idm = $r['id_metamenu'];
        if ($idm === null || !isset($metamenus[$idm])) {
            continue;
        }
        $url = $metamenus[$idm]['url'];
        if ($url === null || trim($url) === '') {
            continue; // contenedor (Raíz), no es un enlace real
        }
        $grupoNom = $tree['grupos'][$r['id_grupmenu']]['nom'] ?? ('grup' . $r['id_grupmenu']);
        $ruta = construyeRuta($grupoNom, $r['id_grupmenu'], $r['orden'], $tree['labels']);

        // Parámetros efectivos = los de la entrada de menú; si vacíos, los del metamenú.
        $params = $r['parametros'];
        if ($params === null || trim((string) $params) === '') {
            $params = $metamenus[$idm]['parametros'] ?? '';
        }
        $params = (string) $params;

        $clave = $url . "\n" . $params;
        if (!isset($indice[$clave])) {
            $indice[$clave] = [
                'url'         => $url,
                'params'      => $params ?? '',
                'id_mod'      => $metamenus[$idm]['id_mod'],
                'descripcion' => $metamenus[$idm]['descripcion'] ?? '',
                'rutas'       => [],
            ];
        }
        $indice[$clave]['rutas'][$etiqueta][$ruta] = $ruta;
    }
}

// --------------------------------------------------------- salida markdown ---

$fecha = date('Y-m-d');
$out = [];
$out[] = '---';
$out[] = 'tipo: referencia';
$out[] = 'titulo: Referencia de menús (generada de la base de datos)';
$out[] = 'fecha: ' . $fecha;
$out[] = 'generado_por: docs/scripts/generar_referencia_menus.php';
$out[] = 'layouts: Legacy (H-dlbv), Pills2 (H-dlpv)';
$out[] = '---';
$out[] = '';
$out[] = '# Referencia de menús de Orbix';
$out[] = '';
$out[] = '> Fichero **generado automáticamente** por `docs/scripts/generar_referencia_menus.php`';
$out[] = '> a partir de la base de datos. No editar a mano: reemplaza al antiguo';
$out[] = '> `docs/legacy/obix/menus.csv` (foto plana anterior a la migración).';
$out[] = '';
$out[] = 'Modelo actual:';
$out[] = '';
$out[] = '- **Metamenús** (`comun.public.aux_metamenus`): el destino (URL + módulo). Son **iguales para todos**.';
$out[] = '- **Menús por layout** (`sv-e."<esquema>".aux_menus`): el árbol de etiquetas y orden, **distinto por layout**.';
$out[] = '  La columna `orden` (array) marca la ruta jerárquica; el grupo de menú (`aux_grupmenu`) es la raíz.';
$out[] = '';
$out[] = 'Layouts documentados: **Legacy** (esquema `H-dlbv`) y **Pills2** (esquema `H-dlpv`).';
$out[] = '';

// 3. Índice URL -> ruta (lo más útil para las guías): primero por ser lo consultado.
$out[] = '## Índice: URL → ruta de menú';
$out[] = '';
$out[] = 'Para citar en las guías. Formato: `Grupo > nivel > … > entrada`.';
$out[] = '';
$out[] = '| Módulo | URL | Parámetros | Descripción | Legacy | Pills2 |';
$out[] = '|--------|-----|------------|-------------|--------|--------|';

$filas = array_values($indice);
usort($filas, function ($a, $b) {
    return [$a['id_mod'] ?? 999, $a['url']] <=> [$b['id_mod'] ?? 999, $b['url']];
});

foreach ($filas as $f) {
    $mod = $f['id_mod'] !== null ? ($modulos[$f['id_mod']] ?? (string) $f['id_mod']) : '';
    $legacy = isset($f['rutas']['Legacy']) ? implode('<br>', array_values($f['rutas']['Legacy'])) : '—';
    $pills2 = isset($f['rutas']['Pills2']) ? implode('<br>', array_values($f['rutas']['Pills2'])) : '—';
    $params = $f['params'] !== '' ? '`' . str_replace('|', '\|', $f['params']) . '`' : '';
    $url = str_replace('|', '\|', $f['url']);
    $desc = str_replace('|', '\|', $f['descripcion']);
    $out[] = "| {$mod} | `{$url}` | {$params} | {$desc} | {$legacy} | {$pills2} |";
}
$out[] = '';

// 2. Árbol por layout.
foreach ($trees as $etiqueta => $tree) {
    $esquema = $LAYOUTS[$etiqueta];
    $out[] = "## Árbol de menús — {$etiqueta} (`{$esquema}`)";
    $out[] = '';

    // ordenar grupos por su 'orden' (nulls al final) y luego entradas por orden array
    $grupos = $tree['grupos'];
    uasort($grupos, function ($a, $b) {
        return [$a['orden'] ?? 9999, $a['nom']] <=> [$b['orden'] ?? 9999, $b['nom']];
    });

    foreach ($grupos as $idG => $g) {
        $entradas = array_filter($tree['rows'], fn($r) => $r['id_grupmenu'] === $idG);
        if ($entradas === []) {
            continue;
        }
        usort($entradas, fn($a, $b) => cmpOrden($a['orden'], $b['orden']));
        $out[] = '### ' . limpiaGrupo($g['nom']);
        $out[] = '';
        foreach ($entradas as $e) {
            $nivel = count($e['orden']) - 1;
            $sangria = str_repeat('  ', $nivel);
            $idm = $e['id_metamenu'];
            $url = ($idm !== null && isset($metamenus[$idm])) ? ($metamenus[$idm]['url'] ?? '') : '';
            $suf = '';
            if ($url !== null && trim((string) $url) !== '') {
                $params = $e['parametros'];
                if ($params === null || trim((string) $params) === '') {
                    $params = $idm !== null ? ($metamenus[$idm]['parametros'] ?? '') : '';
                }
                $paramSuf = ($params !== null && trim((string) $params) !== '') ? "?{$params}" : '';
                $suf = "  → `{$url}{$paramSuf}`";
            }
            $out[] = $sangria . '- ' . $e['menu'] . $suf;
        }
        $out[] = '';
    }
}

// 1. Metamenús.
$out[] = '## Metamenús (destinos URL, iguales para todos)';
$out[] = '';
$out[] = '| id | Módulo | URL | Parámetros | Descripción |';
$out[] = '|----|--------|-----|------------|-------------|';
foreach ($metamenus as $id => $m) {
    $mod = $m['id_mod'] !== null ? ($modulos[$m['id_mod']] ?? (string) $m['id_mod']) : '';
    $url = str_replace('|', '\|', (string) ($m['url'] ?? ''));
    $params = $m['parametros'] !== null && $m['parametros'] !== '' ? '`' . str_replace('|', '\|', $m['parametros']) . '`' : '';
    $desc = str_replace('|', '\|', (string) ($m['descripcion'] ?? ''));
    $out[] = "| {$id} | {$mod} | `{$url}` | {$params} | {$desc} |";
}
$out[] = '';

$contenido = implode("\n", $out) . "\n";

if ($dryRun) {
    echo "\n--- DRY RUN (no se escribe) ---\n";
    echo 'Índice: ' . count($filas) . " entradas (url+params)\n";
    echo 'Salida prevista: ' . $OUTPUT . "\n";
} else {
    file_put_contents($OUTPUT, $contenido);
    echo "\n✅ Escrito: {$OUTPUT}\n";
    echo 'Índice: ' . count($filas) . " entradas (url+params)\n";
}
