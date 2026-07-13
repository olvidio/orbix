<?php

/**
 * Aplica la revisión del módulo zonassacd al catálogo (API + pantallas + flujos).
 * Fuente API: docs/catalogo/zonassacd/api/_metadata_review.json
 * Fuente menús: docs/guias/_referencia_menus.md
 *
 * Uso: php docs/scripts/aplicar_revision_zonassacd.php
 */

declare(strict_types=1);

$repoRoot = dirname(__DIR__, 2);
$catalogo = $repoRoot . '/docs/catalogo/zonassacd';
$metaFile = $catalogo . '/api/_metadata_review.json';
$menusFile = $repoRoot . '/docs/guias/_referencia_menus.md';

if (!is_file($metaFile)) {
    fwrite(STDERR, "Falta $metaFile\n");
    exit(1);
}

/** @var array<string, array<string, mixed>> $meta */
$meta = json_decode((string) file_get_contents($metaFile), true, 512, JSON_THROW_ON_ERROR);

// --- Menús desde índice URL ---
$menuByController = [];
if (is_file($menusFile)) {
    $lines = file($menusFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (!str_contains($line, '| zonassacd |')) {
            continue;
        }
        if (!preg_match(
            '/\|\s*zonassacd\s*\|\s*`([^`]+)`\s*\|[^|]*\|[^|]*\|([^|]+)\|([^|]+)\|/',
            $line,
            $m
        )) {
            continue;
        }
        $url = trim($m[1]);
        if (!str_contains($url, 'frontend/zonassacd/controller/')) {
            continue;
        }
        $legacyRaw = trim(preg_replace('/<br>/', "\n", $m[2]));
        $pills2Raw = trim(preg_replace('/<br>/', "\n", $m[3]));
        $legacy = $legacyRaw;
        foreach (preg_split('/\n+/', $legacyRaw) as $part) {
            $part = trim($part);
            if (str_starts_with($part, 'dre >')) {
                $legacy = $part;
                break;
            }
        }
        $pills2 = $pills2Raw;
        foreach (preg_split('/\n+/', $pills2Raw) as $part) {
            $part = trim($part);
            if (str_contains($part, 'Gestión de zonas')) {
                $pills2 = $part;
                break;
            }
        }
        $basename = basename($url, '.php');
        $menuByController[$basename] = ['legacy' => $legacy, 'pills2' => $pills2];
    }
}

$pantallaResumen = [
    'zona_sacd' => 'Pantalla Zonas-sacd: listar sacd por zona, reasignar zonas (propia/iglesia) y modal de días L–D (vía misas/zona_sacd_datos_*). Requiere perm_des para mutaciones.',
    'zona_ctr' => 'Pantalla Zonas-ctr: listar centros por zona y reasignarlos. Opción sin zona sf solo con perm_des.',
    'zona_sacd_lista_ajax' => 'Fragmento AJAX: tabla sacd (`id_zona`) desde zona_sacd, o listado global si `que=get_lista_tot` (menú Lista sacd-zona).',
    'zona_sacd_update_ajax' => 'Proxy JSON POST a `/src/zonassacd/zona_sacd_update` desde fnjs_guardar en zona_sacd.',
    'zona_ctr_lista_ajax' => 'Fragmento AJAX: renderiza tabla de centros vía zona_ctr_lista (fnjs_busca_ctrs).',
    'zona_ctr_update_ajax' => 'Proxy JSON POST a `/src/zonassacd/zona_ctr_update` desde fnjs_guardar en zona_ctr.',
];

$pantallaSubtipo = [
    'zona_sacd' => 'pantalla_principal',
    'zona_ctr' => 'pantalla_principal',
    'zona_sacd_lista_ajax' => 'fragmento_ajax',
    'zona_sacd_update_ajax' => 'fragmento_ajax',
    'zona_ctr_lista_ajax' => 'fragmento_ajax',
    'zona_ctr_update_ajax' => 'fragmento_ajax',
];

$flujoObjetivo = [
    'zona_sacd' => 'Consultar y gestionar la asignación de sacerdotes (sacd) a zonas geográficas: listado por zona, cambio de zona propia, asignaciones iglesia/cgi y edición de días de atención semanal.',
    'zona_ctr' => 'Consultar y reasignar centros (dl y sf) a zonas geográficas desde el desplegable de zona.',
    'zona_sacd_lista_tot' => 'Ver el listado global sacd–zona de toda la delegación (una fila por asignación). Entrada de menú Lista sacd-zona.',
    'zona_sacd_ajax' => 'Endpoint legacy sin implementación; funcionalidad repartida en zona_sacd_lista, zona_sacd_update y zona_sacd_lista_tot.',
    'zona_ctr_ajax' => 'Endpoint legacy sin implementación; funcionalidad en zona_ctr_lista y zona_ctr_update.',
];

$flujoParentMenu = [
    'zona_sacd' => 'zona_sacd',
    'zona_ctr' => 'zona_ctr',
    'zona_sacd_lista_tot' => 'zona_sacd_lista_ajax',
    'zona_sacd_ajax' => null,
    'zona_ctr_ajax' => null,
];

function parseFrontMatter(string $content): array
{
    if (!preg_match('/^---\n(.*?)\n---\n(.*)$/s', $content, $m)) {
        return ['', $content];
    }
    return [$m[1], $m[2]];
}

function setFmField(string $fm, string $key, string $value): string
{
    if (preg_match('/^' . preg_quote($key, '/') . ':.*$/m', $fm)) {
        return (string) preg_replace('/^' . preg_quote($key, '/') . ':.*$/m', "$key: $value", $fm);
    }
    return $fm . "\n$key: $value";
}

function yamlList(array $items): string
{
    if ($items === []) {
        return '[]';
    }
    return '[' . implode(', ', array_map(static fn($i) => '"' . addslashes((string) $i) . '"', $items)) . ']';
}

function formatEntradaFm(array $entrada): string
{
    $parts = [];
    foreach ($entrada as $k => $v) {
        $parts[] = 'post.' . $k . ':' . (str_contains((string) $v, 'integer') ? 'integer' : 'string');
    }
    return yamlList($parts);
}

function formatSalidaText(array $salida, string $operacion): string
{
    $lines = ["- Helper: `ContestarJson::enviar`.", "- Forma: `standard_envelope_string_data`."];
    $success = $salida['success_data'] ?? null;
    if ($success === null) {
        $lines[] = '- Sin salida estándar (ruta muerta o pendiente).';
    } elseif ($success === 'ok') {
        $lines[] = '- Exito: `success: true`, `data: "ok"`. Errores parciales en `mensaje`.';
    } elseif ($operacion === 'lista_data' || $operacion === 'form_data') {
        $lines[] = '- Claves en `data` (doble `JSON.parse` salvo vacío):';
        foreach ($success as $k => $v) {
            $lines[] = "  - `$k`: $v";
        }
    } else {
        $lines[] = '- Exito: payload en `data`.';
    }
    return implode("\n", $lines);
}

function menuSection(?array $menu): string
{
    if ($menu === null) {
        return "## Ruta de menú\n\n- **Legacy:** sin entrada de menú en el índice\n- **Pills2:** sin entrada de menú en el índice\n";
    }
    return "## Ruta de menú\n\n- **Legacy:** {$menu['legacy']}\n- **Pills2:** {$menu['pills2']}\n";
}

function titleFromName(string $name): string
{
    $map = [
        'zona_sacd' => 'Zona Sacd',
        'zona_sacd_ajax' => 'Zona Sacd Ajax',
        'zona_sacd_lista' => 'Zona Sacd Lista',
        'zona_sacd_lista_tot' => 'Zona Sacd Lista Tot',
        'zona_sacd_update' => 'Zona Sacd Update',
        'zona_ctr' => 'Zona Ctr',
        'zona_ctr_ajax' => 'Zona Ctr Ajax',
        'zona_ctr_lista' => 'Zona Ctr Lista',
        'zona_ctr_update' => 'Zona Ctr Update',
    ];
    return $map[$name] ?? ucwords(str_replace('_', ' ', $name));
}

// ========== API ==========
$apiCount = 0;
foreach (glob($catalogo . '/api/*.md') as $path) {
    $name = basename($path, '.md');
    if ($name === '_metadata_review' || !isset($meta[$name])) {
        continue;
    }
    $m = $meta[$name];
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $operacion = $m['operacion'];
    $entrada = $m['entrada'] ?? [];
    $oblig = $m['entrada_obligatoria'] ?? [];
    $errores = $m['errores'] ?? [];

    $fm = setFmField($fm, 'operacion', '"' . $operacion . '"');
    $fm = setFmField($fm, 'entrada', formatEntradaFm($entrada));
    $fm = setFmField($fm, 'entrada_obligatoria', yamlList($oblig));
    $fm = setFmField($fm, 'errores', yamlList($errores));
    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $title = titleFromName($name);
    $entradaRows = '';
    foreach ($entrada as $k => $v) {
        $ob = in_array($k, $oblig, true) ? 'Si' : 'No';
        $entradaRows .= "| `$k` | `$v` | application | $ob | |\n";
    }
    if ($entradaRows === '') {
        $entradaRows = "| _(ninguno)_ | | | | |\n";
    }

    $permisos = $m['permisos'] ?? [];
    $permText = $permisos === []
        ? 'Sin control de permisos propio en el caso de uso; la autorización se resuelve en frontend + `$_SESSION[\'oPerm\']`.'
        : implode(' ', $permisos);

    $lineage = isset($m['apps_lineage']) ? "\n\nLinaje: {$m['apps_lineage']}." : '';
    $casos = $m['casos_uso'] ?? [];
    $casosList = $casos === []
        ? '- _(ninguno — ruta muerta)_'
        : implode("\n", array_map(static fn($c) => "- `$c`", $casos));

    preg_match('/frontend_referencias:\s*(\[[^\]]*\])/', $fm, $frMatch);
    $fr = $frMatch[1] ?? '[]';

    $salidaText = formatSalidaText($m['salida'] ?? [], $operacion);
    $objetivo = $m['objetivo_funcional'];

    $newBody = <<<MD
# {$title}

{$objetivo}{$lineage}

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

{$objetivo}

## Endpoint

- URL: `/src/zonassacd/{$name}`
- Metodos registrados: `GET, POST`
- Operacion: `{$operacion}`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/{$name}.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
{$entradaRows}
## Salida

{$salidaText}

## Errores conocidos

MD;
    if ($errores === []) {
        $newBody .= "\n- _(ninguno documentado en casos de uso)_\n";
    } else {
        foreach ($errores as $e) {
            $newBody .= "- `$e`\n";
        }
    }

    $newBody .= <<<MD

## Permisos

{$permText}

## Casos De Uso

{$casosList}

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`{$fr}`).

MD;

    file_put_contents($path, "---\n$fm\n---\n\n$newBody");
    $apiCount++;
}

// ========== PANTALLAS ==========
$pantCount = 0;
foreach (glob($catalogo . '/pantallas/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $resumen = $pantallaResumen[$name] ?? 'Pantalla del módulo zonassacd.';
    $subtipo = $pantallaSubtipo[$name] ?? 'fragmento_ajax';
    $menu = $menuByController[$name] ?? null;
    if (in_array($name, ['zona_sacd_update_ajax', 'zona_ctr_lista_ajax', 'zona_ctr_update_ajax'], true)) {
        $menu = null;
    }

    $fm = setFmField($fm, 'subtipo', '"' . $subtipo . '"');
    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $sections = [];
    if (preg_match('/## Acciones \(revisadas\)\n(.*?)(?=\n## |\z)/s', $body, $m)) {
        $sections['acciones_revisadas'] = "## Acciones (revisadas)\n" . $m[1];
    }
    foreach (['Vistas Relacionadas', 'Fragmentos Frontend Relacionados', 'Endpoints Usados', 'Capacidades Relacionadas', 'Campos Detectados', 'Acciones Detectadas'] as $sec) {
        if (preg_match('/## ' . preg_quote($sec, '/') . '\n(.*?)(?=\n## |\z)/s', $body, $m)) {
            $sections[$sec] = "## {$sec}\n" . $m[1];
        }
    }

    $title = titleFromName($name);
    $newBody = "# {$title}\n\n{$resumen}\n\n";
    $newBody .= "## Tipo\n\n- Subtipo: `{$subtipo}`\n";
    $newBody .= "- Controller: `frontend/zonassacd/controller/{$name}.php`\n\n";
    foreach ($sections as $s) {
        $newBody .= rtrim($s) . "\n\n";
    }
    $newBody .= menuSection($menu);
    $newBody = rtrim($newBody) . "\n";

    file_put_contents($path, "---\n$fm\n---\n\n$newBody");
    $pantCount++;
}

// ========== FLUJOS ==========
$flujoEndpointMap = [];
foreach ($meta as $endpoint => $m) {
    $flujoEndpointMap['/src/zonassacd/' . $endpoint] = $m;
}

$flujCount = 0;
foreach (glob($catalogo . '/flujos/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $baseName = preg_replace('/_ajax$/', '_ajax', $name);
    $objetivo = $flujoObjetivo[$name] ?? "Flujo de usuario del módulo zonassacd (`{$name}`).";

    $parent = $flujoParentMenu[$name] ?? $name;
    $menu = $parent ? ($menuByController[$parent] ?? null) : null;

    preg_match('/endpoints:\s*(\[[^\]]*\])/', $fm, $epMatch);
    $errores = [];
    if (!empty($epMatch[1])) {
        preg_match_all('/"([^"]+)"/', $epMatch[1], $eps);
        foreach ($eps[1] ?? [] as $ep) {
            if (isset($flujoEndpointMap[$ep])) {
                $errores = array_merge($errores, $flujoEndpointMap[$ep]['errores'] ?? []);
            }
        }
    }
    $errores = array_values(array_unique($errores));

    $puntoEntrada = $menu
        ? "Menú Legacy: {$menu['legacy']}. Pills2: {$menu['pills2']}."
        : 'Sin entrada de menú directa; fragmento o endpoint legacy.';

    $keepSections = [];
    foreach (['Fragmentos O Pantallas Auxiliares', 'Endpoints Del Flujo'] as $sec) {
        if (preg_match('/## ' . preg_quote($sec, '/') . '\n(.*?)(?=\n## |\z)/s', $body, $m)) {
            $keepSections[$sec] = "## {$sec}\n" . $m[1];
        }
    }

    $title = 'Flujo - ' . titleFromName($name);
    $newBody = "# {$title}\n\n";
    $newBody .= "## Objetivo De Usuario\n\n{$objetivo}\n\n";
    $newBody .= "## Punto De Entrada\n\n{$puntoEntrada}\n\n";

    if ($name === 'zona_sacd') {
        $newBody .= "## Escenarios\n\n";
        $newBody .= "### Consultar sacd de una zona\n\n";
        $newBody .= "1. Abrir Zonas-sacd desde el menú.\n";
        $newBody .= "2. Elegir zona (o «sin asignar zona») en el desplegable → carga AJAX `zona_sacd_lista`.\n";
        $newBody .= "3. Revisar tabla: sacd, zona, propia, días L–D.\n\n";
        $newBody .= "### Cambiar asignación de zona (perm_des)\n\n";
        $newBody .= "1. Marcar sacd en la tabla.\n";
        $newBody .= "2. Elegir zona destino y pulsar «cambiar asignación zona» (`acumular=1`) o «añadir asignación iglesia/cgi» (`acumular=2`).\n";
        $newBody .= "3. Validaciones cliente: zona destino y al menos un sacd marcado.\n\n";
        $newBody .= "### Editar días de atención (perm_des)\n\n";
        $newBody .= "1. Marcar un solo sacd → botón «modificar» → modal.\n";
        $newBody .= "2. GET `/src/misas/zona_sacd_datos_get`; grabar con PUT `/src/misas/zona_sacd_datos_put`.\n\n";
    } elseif ($name === 'zona_ctr') {
        $newBody .= "## Escenarios\n\n";
        $newBody .= "### Consultar centros de una zona\n\n";
        $newBody .= "1. Abrir Zonas-ctr.\n";
        $newBody .= "2. Elegir zona (`int`, `no`, o `no_sf` con perm_des) → AJAX `zona_ctr_lista`.\n\n";
        $newBody .= "### Reasignar centros (perm_des)\n\n";
        $newBody .= "1. Marcar centros, elegir zona destino (o «sin asignar zona»).\n";
        $newBody .= "2. Pulsar «asignar» → `zona_ctr_update`.\n\n";
    } elseif ($name === 'zona_sacd_lista_tot') {
        $newBody .= "## Escenarios\n\n";
        $newBody .= "1. Menú «Lista sacd-zona» carga `zona_sacd_lista_ajax.php?que=get_lista_tot`.\n";
        $newBody .= "2. Muestra listado HTML de todos los sacd con sus zonas (endpoint `zona_sacd_lista_tot`).\n\n";
    }

    foreach ($keepSections as $s) {
        $newBody .= rtrim($s) . "\n\n";
    }
    $newBody .= "## Errores Conocidos\n\n";
    if ($errores === []) {
        $newBody .= "- _(ninguno documentado)_\n\n";
    } else {
        foreach ($errores as $e) {
            $newBody .= "- `$e`\n";
        }
        $newBody .= "\n";
    }
    $newBody .= menuSection($menu);
    $newBody = rtrim($newBody) . "\n";

    file_put_contents($path, "---\n$fm\n---\n\n$newBody");
    $flujCount++;
}

echo "✅ API: $apiCount | Pantallas: $pantCount | Flujos: $flujCount\n";
