<?php

/**
 * Aplica la revisión del módulo ubis al catálogo (API + pantallas + flujos).
 * Fuente API: docs/catalogo/ubis/api/_metadata_review.json
 * Fuente menús: docs/guias/_referencia_menus.md
 *
 * Uso: php docs/scripts/aplicar_revision_ubis.php
 */

declare(strict_types=1);

$repoRoot = dirname(__DIR__, 2);
$catalogo = $repoRoot . '/docs/catalogo/ubis';
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
        if (!preg_match('/\|\s*(ubis|base|activ1)\s*\|\s*`([^`]+)`\s*\|[^|]*\|[^|]*\|([^|]+)\|([^|]+)\|/', $line, $m)) {
            continue;
        }
        $mod = trim($m[1]);
        $url = trim($m[2]);
        if (!str_contains($url, 'frontend/ubis/controller/')) {
            continue;
        }
        $legacyRaw = trim(preg_replace('/<br>/', "\n", $m[3]));
        $pills2Raw = trim($m[4]);
        $legacy = $legacyRaw;
        foreach (preg_split('/\n+/', $legacyRaw) as $part) {
            $part = trim($part);
            if ($mod === 'ubis' && str_starts_with($part, 'scdl >')) {
                $legacy = $part;
                break;
            }
            if ($mod === 'activ1' && str_contains($part, 'Definir periodos')) {
                $legacy = $part;
                break;
            }
            if ($mod === 'base' && str_starts_with($part, 'vsg >')) {
                $legacy = $part;
                break;
            }
        }
        $pills2 = $pills2Raw;
        foreach (preg_split('/\n+/', $pills2Raw) as $part) {
            $part = trim($part);
            if (str_contains($part, 'CASAS Y CTR') || str_contains($part, 'Herramientas de calendario')) {
                $pills2 = $part;
                break;
            }
        }
        $pills2 = trim(preg_replace('/<br>.*/', '', $pills2));
        $basename = basename($url, '.php');
        if (!isset($menuByController[$basename])) {
            $menuByController[$basename] = ['legacy' => $legacy, 'pills2' => $pills2];
        }
    }
}

$pantallaResumen = [
    'plano_bytea' => 'Sube, descarga o elimina el plano (bytea) asociado a una dirección.',
    'trasladar_ubis' => 'Ejecuta el traslado de ubis seleccionados a la delegación destino indicada.',
    'direcciones_editar' => 'Muestra y edita las direcciones vinculadas a un ubi dentro de la ficha.',
    'list_ctr' => 'Pantalla principal de listado de centros y casas con filtros por delegación y tipo.',
    'teleco_tabla' => 'Tabla AJAX de telecomunicaciones de un ubi con acciones modificar y eliminar.',
    'ubis_tabla' => 'Tabla de resultados de búsqueda de ubis con navegación y acciones sobre selección.',
    'home_ubis' => 'Ficha resumen de un ubi con enlaces a edición, direcciones, telecomunicaciones y dossiers.',
    'ubis_lista' => 'Fragmento HTML de resultados de autocompletado al buscar ubis por nombre.',
    'lista_ctrs' => 'Tabla AJAX de centros tipo s con recuento de sacerdotes por centro.',
    'centros_form_plazas' => 'Formulario modal para editar plazas, habitaciones y sede de un centro DL.',
    'ubis_buscar' => 'Formulario de criterios de búsqueda de centros y casas que alimenta ubis_tabla.',
    'calendario_periodos' => 'Pantalla principal de gestión de periodos de calendario de casas CDC por año.',
    'delegacion_que' => 'Modal de selección de delegación destino para trasladar ubis desde list_ctr.',
    'ubis_eliminar' => 'Fragmento que invoca la eliminación de un ubi y muestra error si falla.',
    'direcciones_quitar' => 'Desvincula una dirección del ubi tras confirmación en la ficha de direcciones.',
    'centros_get_plazas' => 'Tabla AJAX de plazas y sede de todos los centros DL activos.',
    'direcciones_asignar' => 'Asigna una dirección existente al ubi desde la tabla de búsqueda.',
    'ubis_update' => 'Guarda los cambios del formulario de edición de ubi vía API ubis_guardar.',
    'centros_get_num' => 'Tabla AJAX de datos numéricos (buzón, pi, cartas) de centros DL.',
    'calendario_periodos_get2' => 'Tabla AJAX de periodos de calendario de una casa en un año con aviso de solapes.',
    'direccion_update' => 'Persiste creación o modificación de una dirección y su relación con el ubi.',
    'centros_get_labor' => 'Tabla AJAX de tipo de labor de todos los centros DL activos.',
    'teleco_desc_lista_ajax' => 'Actualiza el desplegable de descripciones al cambiar el tipo de telecomunicación.',
    'ubis_editar' => 'Formulario de edición o alta de ficha de centro o casa dentro de la ficha ubi.',
    'teleco_editar' => 'Formulario modal de alta o edición de una telecomunicación del ubi.',
    'direcciones_tabla' => 'Tabla AJAX de direcciones encontradas para asignar a un ubi.',
    'centros_form_num' => 'Formulario modal para editar buzón, pi y cartas de un centro DL.',
    'centros_form_labor' => 'Formulario modal para editar tipo de centro y tipo de labor de un centro DL.',
    'calendario_periodos_nuevo' => 'Formulario modal de alta de periodo de calendario con fechas y sfsv sugeridos.',
    'calendario_periodos_get' => 'Vista AJAX legacy de periodos de calendario con acciones grabar y borrar inline.',
    'calendario_periodos_form_periodo' => 'Formulario modal de edición o eliminación de un periodo de calendario existente.',
    'info_ubis' => 'Pantalla informativa estática sobre el módulo de ubis (sin menú directo).',
    'centros_que' => 'Pantalla principal de consulta y edición masiva de datos de centros DL (labor, num, plazas).',
    'direcciones_que' => 'Formulario de criterios para buscar direcciones existentes a asignar a un ubi.',
    'calendario_periodos_ajax' => 'Dispatcher que enruta por parámetro que a los fragmentos de calendario de periodos.',
];

$pantallaSubtipo = [
    'plano_bytea' => 'descarga',
    'trasladar_ubis' => 'fragmento_ajax',
    'direcciones_editar' => 'fragmento_ajax',
    'list_ctr' => 'pantalla_principal',
    'teleco_tabla' => 'fragmento_ajax',
    'ubis_tabla' => 'pantalla_principal',
    'home_ubis' => 'fragmento_ajax',
    'ubis_lista' => 'fragmento_ajax',
    'lista_ctrs' => 'fragmento_ajax',
    'centros_form_plazas' => 'modal',
    'ubis_buscar' => 'pantalla_principal',
    'calendario_periodos' => 'pantalla_principal',
    'delegacion_que' => 'modal',
    'ubis_eliminar' => 'fragmento_ajax',
    'direcciones_quitar' => 'fragmento_ajax',
    'centros_get_plazas' => 'fragmento_ajax',
    'direcciones_asignar' => 'fragmento_ajax',
    'ubis_update' => 'fragmento_ajax',
    'centros_get_num' => 'fragmento_ajax',
    'calendario_periodos_get2' => 'fragmento_ajax',
    'direccion_update' => 'fragmento_ajax',
    'centros_get_labor' => 'fragmento_ajax',
    'teleco_desc_lista_ajax' => 'fragmento_ajax',
    'ubis_editar' => 'fragmento_ajax',
    'teleco_editar' => 'modal',
    'direcciones_tabla' => 'fragmento_ajax',
    'centros_form_num' => 'modal',
    'centros_form_labor' => 'modal',
    'calendario_periodos_nuevo' => 'modal',
    'calendario_periodos_get' => 'fragmento_ajax',
    'calendario_periodos_form_periodo' => 'modal',
    'info_ubis' => 'pantalla_principal',
    'centros_que' => 'pantalla_principal',
    'direcciones_que' => 'fragmento_ajax',
    'calendario_periodos_ajax' => 'dispatcher',
];

$parentMenu = [
    'ubis_editar_load' => 'ubis_editar',
    'trasladar_ubis' => 'list_ctr',
    'teleco_tabla' => 'home_ubis',
    'ubis_editar' => 'home_ubis',
    'ubis_buscar' => 'ubis_buscar',
    'teleco_editar' => 'teleco_tabla',
    'ubis_editar_normalize_dl' => 'ubis_editar',
    'ubis' => null,
    'ubis_tabla' => 'ubis_buscar',
    'centros_form_num' => 'centros_que',
    'teleco_desc' => 'teleco_editar',
    'centros_form_plazas' => 'centros_que',
    'centros' => 'centros_que',
    'home_ubis' => null,
    'direcciones_quitar' => 'direcciones_editar',
    'teleco' => 'home_ubis',
    'direccion' => 'direcciones_editar',
    'direcciones_asignar' => 'direcciones_tabla',
    'direcciones_tabla' => 'direcciones_que',
    'centros_get_labor' => 'centros_que',
    'centros_get_plazas' => 'centros_que',
    'lista_ctrs' => 'lista_ctrs',
    'list_ctr' => 'list_ctr',
    'centros_get_num' => 'centros_que',
    'direcciones_editar' => 'home_ubis',
    'direcciones_que' => 'direcciones_editar',
    'centros_form_labor' => 'centros_que',
    'delegacion_que' => 'list_ctr',
    'centros_opciones' => null,
    'delegaciones_region_stgr' => null,
    'calendario_periodos_get' => 'calendario_periodos',
    'calendario_periodos_form_periodo' => 'calendario_periodos',
    'casas_opciones' => null,
    'calendario_periodos' => 'calendario_periodos',
    'calendario_periodos_nuevo' => 'calendario_periodos',
    'calendario_periodos_get2' => 'calendario_periodos',
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
        $type = str_contains((string) $v, 'integer') ? 'integer' : 'string';
        $parts[] = 'post.' . $k . ':' . $type;
    }
    return yamlList($parts);
}

function formatSalidaText(array $salida, string $operacion): string
{
    $lines = ["- Helper: `ContestarJson::enviar`.", "- Forma: `standard_envelope_string_data`."];
    $success = $salida['success_data'] ?? null;
    if ($success === 'ok' || $success === null) {
        $lines[] = '- Exito: `success: true`, `data: "ok"` (string vacío serializado).';
    } elseif (is_array($success) && $success === []) {
        $lines[] = '- Exito: `success: true`, `data: "{}"`.';
    } elseif ($operacion === 'lista_data' || $operacion === 'form_data') {
        $lines[] = '- Claves en `data` (doble `JSON.parse`):';
        foreach ($success as $k => $v) {
            $lines[] = "  - `$k`: $v";
        }
    } else {
        $lines[] = '- Exito: payload en `data`:';
        foreach ($success as $k => $v) {
            $vStr = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string) $v;
            $lines[] = "  - `$k`: $vStr";
        }
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
    $t = str_replace('_', ' ', $name);
    return ucwords($t);
}

$permDefault = 'Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION[\'oPerm\']`.';

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
    $permText = $permisos === [] ? $permDefault : implode(' ', $permisos);

    $casos = $m['casos_uso'] ?? [];
    $casosList = implode("\n", array_map(static fn($c) => "- `src\\ubis\\application\\$c`", $casos));

    preg_match('/frontend_referencias:\s*(\[[^\]]*\])/', $fm, $frMatch);
    $fr = $frMatch[1] ?? '[]';

    $salidaText = formatSalidaText($m['salida'] ?? [], $operacion);
    $objetivo = $m['objetivo_funcional'];

    $newBody = <<<MD
# {$title}

{$objetivo}

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

{$objetivo}

## Endpoint

- URL: `/src/ubis/{$name}`
- Metodos registrados: `GET, POST`
- Operacion: `{$operacion}`
- Controller: `src/ubis/infrastructure/ui/http/controllers/{$name}.php`

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

    $resumen = $pantallaResumen[$name] ?? 'Pantalla del módulo ubis (centros, casas, direcciones y telecomunicaciones).';
    $subtipo = $pantallaSubtipo[$name] ?? 'fragmento_ajax';
    $menu = $menuByController[$name] ?? null;

    $fm = setFmField($fm, 'subtipo', '"' . $subtipo . '"');
    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $sections = [];
    if (preg_match('/## Tipo\n(.*?)(?=\n## |\z)/s', $body, $m)) {
        $sections['tipo'] = "## Tipo\n\n- Subtipo: `{$subtipo}`\n" . preg_replace('/- Subtipo:.*\n/', '', $m[1]);
    }
    foreach (['Vistas Relacionadas', 'Fragmentos Frontend Relacionados', 'Endpoints Usados', 'Capacidades Relacionadas', 'Campos Detectados', 'Acciones Detectadas'] as $sec) {
        if (preg_match('/## ' . preg_quote($sec, '/') . '\n(.*?)(?=\n## |\z)/s', $body, $m)) {
            $sections[$sec] = "## {$sec}\n" . $m[1];
        }
    }

    $title = titleFromName($name);
    $newBody = "# {$title}\n\n{$resumen}\n\n";
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
    $flujoEndpointMap['/src/ubis/' . $endpoint] = $m;
}

$flujCount = 0;
foreach (glob($catalogo . '/flujos/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    preg_match('/endpoints:\s*(\[[^\]]*\])/', $fm, $epMatch);
    $endpointMeta = null;
    if (!empty($epMatch[1])) {
        preg_match_all('/"([^"]+)"/', $epMatch[1], $eps);
        foreach ($eps[1] ?? [] as $ep) {
            if (isset($flujoEndpointMap[$ep])) {
                $endpointMeta = $flujoEndpointMap[$ep];
                break;
            }
        }
    }

    $objetivo = $endpointMeta['objetivo_funcional'] ?? "Flujo de usuario del módulo ubis (`{$name}`).";
    $errores = $endpointMeta['errores'] ?? [];

    $menu = null;
    if (preg_match('/fragmentos:\s*\["ubis\.pantalla\.([^"]+)"\]/', $fm, $frag)) {
        $menu = $menuByController[$frag[1]] ?? null;
    }
    if (preg_match('/pantallas_principales:\s*\["ubis\.pantalla\.([^"]+)"\]/', $fm, $pp)) {
        $menu = $menuByController[$pp[1]] ?? $menu;
    }

    if ($menu === null && isset($parentMenu[$name])) {
        $parent = $parentMenu[$name];
        $menu = $parent ? ($menuByController[$parent] ?? null) : null;
    }

    $title = 'Flujo - ' . titleFromName($name);

    $keepSections = [];
    foreach ([
        'Fragmentos O Pantallas Auxiliares',
        'Escenarios Inferidos',
        'Campos Y Acciones Detectadas En Pantalla',
        'Endpoints Del Flujo',
    ] as $sec) {
        if (preg_match('/## ' . preg_quote($sec, '/') . '\n(.*?)(?=\n## |\z)/s', $body, $m)) {
            $keepSections[$sec] = "## {$sec}\n" . $m[1];
        }
    }

    $puntoEntrada = $menu
        ? "Menú Legacy: {$menu['legacy']}. Pills2: {$menu['pills2']}."
        : 'Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.';

    $newBody = "# {$title}\n\n";
    $newBody .= "## Objetivo De Usuario\n\n{$objetivo}\n\n";
    $newBody .= "## Punto De Entrada\n\n{$puntoEntrada}\n\n";
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
