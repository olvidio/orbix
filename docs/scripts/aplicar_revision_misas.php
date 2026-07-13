<?php

/**
 * Aplica la revisión del módulo misas al catálogo (API + pantallas + flujos).
 * Fuente API: docs/catalogo/misas/api/_metadata_review.json
 * Fuente menús: docs/guias/_referencia_menus.md
 *
 * Uso: php docs/scripts/aplicar_revision_misas.php
 */

declare(strict_types=1);

$repoRoot = dirname(__DIR__, 2);
$catalogo = $repoRoot . '/docs/catalogo/misas';
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
        if (!str_contains($line, '| misas |')) {
            continue;
        }
        // | misas | `frontend/misas/controller/X.php` |  | desc | legacy | pills2 |
        if (!preg_match(
            '/\|\s*misas\s*\|\s*`([^`]+)`\s*\|[^|]*\|[^|]*\|([^|]+)\|([^|]+)\|/',
            $line,
            $m
        )) {
            continue;
        }
        $url = trim($m[1]);
        $legacyRaw = trim(preg_replace('/<br>/', "\n", $m[2]));
        $pills2Raw = trim($m[3]);
        // Preferir ruta dre en Legacy; primera ATENCIÓN SACD > Gestión de misas en Pills2
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
            if (str_contains($part, 'Gestión de misas')) {
                $pills2 = $part;
                break;
            }
        }
        $basename = basename($url, '.php');
        $menuByController[$basename] = ['legacy' => $legacy, 'pills2' => $pills2];
    }
}

// --- Resúmenes pantallas ---
$pantallaResumen = [
    'misas_index' => 'Índice de navegación del módulo con enlaces HashFront a las 10 pantallas principales (plan, encargos, plantilla, iniciales, status). Sin backend JSON.',
    'modificar_iniciales_sacd_zona' => 'Entry point para editar iniciales y color de sacerdotes por zona. Selector de zona y carga AJAX de `ver_iniciales_zona`.',
    'ver_iniciales_zona' => 'Fragmento SlickGrid con sacds de la zona; edición inline que postea a `update_iniciales`.',
    'modificar_encargos' => 'Entry point para CRUD de encargos de zona (grupo ZONAS_MISAS). Selectores zona/orden y grid AJAX `ver_encargos_zona`.',
    'ver_encargos_zona' => 'Fragmento SlickGrid de encargos 8100+ con modal alta/edición/borrado (`guardar_encargo_zona`, `eliminar_encargo_zona`).',
    'modificar_encargos_centros' => 'Entry point para vincular encargos de zona con centros (EncargoCtr). Selector de zona y grid `ver_encargos_centros`.',
    'ver_encargos_centros' => 'Fragmento SlickGrid EncargoCtr con modal y desplegables dinámicos (`desplegable_encargos`, `desplegable_centros_zona`).',
    'preparar_plan_de_misas' => 'Formulario para crear un nuevo plan de misas: zona, orden, tipo plantilla y periodo. Redirige a `crear_nuevo_periodo`.',
    'modificar_plan_de_misas' => 'Formulario para editar plan existente: zona, orden y rango de fechas. Carga cuadrícula `ver_cuadricula_zona`.',
    'ver_plan_de_misas' => 'Consulta del plan de misas de una zona en modo solo lectura (cuadrícula sin edición de celdas).',
    'crear_nuevo_periodo' => 'Fragmento que ejecuta `crear_nuevo_periodo_data` y renderiza `ver_cuadricula_zona.phtml` con el nuevo periodo.',
    'ver_cuadricula_zona' => 'Cuadrícula SlickGrid de asignaciones EncargoDia (filas sacd × columnas día/encargo). Edición vía `cuadricula_update` y `desplegable_sacd`.',
    'modificar_cuadricula_zona' => 'Alias de edición de cuadrícula; comparte vista y endpoints con `ver_cuadricula_zona` en modo modificar.',
    'modificar_plantilla' => 'Editor de plantilla semanal (-1): centros/tareas/horarios por zona. Grid plantilla + `anadir_ctr_tarea`, `quitar_horario`, `importar_plantilla`.',
    'importar_plantilla' => 'Fragmento AJAX que copia asignaciones entre tipos de plantilla (`importar_plantilla_data`).',
    'horario_tarea' => 'Modal para editar hora inicio/fin de una tarea en plantilla (`horario_tarea_data`, `guardar_horario`).',
    'cambiar_status' => 'Pantalla para cambio masivo de estado de encargos en un rango de fechas (`cambiar_status_data`, `nuevo_status`).',
    'buscar_plan_sacd' => 'Buscador de plan por sacerdote: desplegable sacd filtrado por rol y rango de fechas.',
    'ver_plan_sacd' => 'Resultado: lista cronológica de misas del sacerdote (`ver_plan_sacd_data`).',
    'buscar_plan_ctr' => 'Buscador de plan por centro: zona y centro según rol (ctr/sacd/jefe).',
    'ver_plan_ctr' => 'Resultado: cuadrícula encargo×día del centro (`ver_plan_ctr_data`) con leyenda de sacds.',
    'imprimir_plan_ctr' => 'Generación PDF/mpdf del plan CTR a partir de `ver_plan_ctr`. Sin menú directo.',
    'ver_misas_zona' => 'Consulta de misas por zona y fechas (solo lectura). Sin entrada de menú en el índice; acceso vía enlaces internos.',
];

/** @var array<string, string> pantalla_principal vs fragmento */
$pantallaSubtipo = [
    'misas_index' => 'pantalla_principal',
    'modificar_iniciales_sacd_zona' => 'pantalla_principal',
    'ver_iniciales_zona' => 'fragmento_ajax',
    'modificar_encargos' => 'pantalla_principal',
    'ver_encargos_zona' => 'fragmento_ajax',
    'modificar_encargos_centros' => 'pantalla_principal',
    'ver_encargos_centros' => 'fragmento_ajax',
    'preparar_plan_de_misas' => 'pantalla_principal',
    'modificar_plan_de_misas' => 'pantalla_principal',
    'ver_plan_de_misas' => 'pantalla_principal',
    'crear_nuevo_periodo' => 'fragmento_ajax',
    'ver_cuadricula_zona' => 'fragmento_ajax',
    'modificar_cuadricula_zona' => 'fragmento_ajax',
    'modificar_plantilla' => 'pantalla_principal',
    'importar_plantilla' => 'fragmento_ajax',
    'horario_tarea' => 'modal',
    'cambiar_status' => 'pantalla_principal',
    'buscar_plan_sacd' => 'pantalla_principal',
    'ver_plan_sacd' => 'fragmento_ajax',
    'buscar_plan_ctr' => 'pantalla_principal',
    'ver_plan_ctr' => 'fragmento_ajax',
    'imprimir_plan_ctr' => 'descarga',
    'ver_misas_zona' => 'pantalla_principal',
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
        $lines[] = '- Exito: `success: true`, `data: "ok"` (string vacio serializado).';
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

    $title = str_replace('_', ' ', ucwords(str_replace('_data', ' Data', $name)));
    $title = preg_replace('/\bData\b/', 'Data', $title);

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
        ? 'Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION[\'oPerm\']`.'
        : implode(' ', $permisos);

    $lineage = isset($m['apps_lineage']) ? "\n\nLinaje: {$m['apps_lineage']}" : '';

    $casos = $m['casos_uso'] ?? [];
    $casosList = implode("\n", array_map(static fn($c) => "- `$c`", $casos));

    // preserve frontend_referencias from original fm
    preg_match('/frontend_referencias:\s*(\[[^\]]*\])/', $fm, $frMatch);
    $fr = $frMatch[1] ?? '[]';

    $salidaText = formatSalidaText($m['salida'] ?? [], $operacion);

    $newBody = <<<MD
# {$title}

{$m['objetivo_funcional']}{$lineage}

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

{$m['objetivo_funcional']}

## Endpoint

- URL: `/src/misas/{$name}`
- Metodos registrados: `GET, POST`
- Operacion: `{$operacion}`
- Controller: `src/misas/infrastructure/ui/http/controllers/{$name}.php`

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

    $resumen = $pantallaResumen[$name] ?? 'Pantalla del módulo misas (gestión de planes, encargos y plantillas).';
    $subtipo = $pantallaSubtipo[$name] ?? 'fragmento_ajax';
    $menu = $menuByController[$name] ?? null;

    $fm = setFmField($fm, 'subtipo', '"' . $subtipo . '"');
    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    // rebuild body keeping structural sections from original
    $sections = [];
    if (preg_match('/## Tipo\n(.*?)(?=\n## |\z)/s', $body, $m)) {
        $sections['tipo'] = "## Tipo\n\n- Subtipo: `{$subtipo}`\n" . preg_replace('/- Subtipo:.*\n/', '', $m[1]);
    }
    foreach (['Vistas Relacionadas', 'Fragmentos Frontend Relacionados', 'Endpoints Usados', 'Capacidades Relacionadas', 'Campos Detectados', 'Acciones Detectadas'] as $sec) {
        if (preg_match('/## ' . preg_quote($sec, '/') . '\n(.*?)(?=\n## |\z)/s', $body, $m)) {
            $sections[$sec] = "## {$sec}\n" . $m[1];
        }
    }

    $title = str_replace('_', ' ', ucwords($name));
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
    $flujoEndpointMap['/src/misas/' . $endpoint] = $m;
}

$flujCount = 0;
foreach (glob($catalogo . '/flujos/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    // extract endpoint from fm
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

    $objetivo = $endpointMeta['objetivo_funcional'] ?? "Flujo de usuario del módulo misas (`{$name}`).";
    $errores = $endpointMeta['errores'] ?? [];

    // infer menu from pantallas_principales or fragmentos
    $menu = null;
    if (preg_match('/fragmentos:\s*\["misas\.pantalla\.([^"]+)"\]/', $fm, $frag)) {
        $menu = $menuByController[$frag[1]] ?? null;
    }
    if (preg_match('/pantallas_principales:\s*\["misas\.pantalla\.([^"]+)"\]/', $fm, $pp)) {
        $menu = $menuByController[$pp[1]] ?? $menu;
    }

    // parent entry screens for common flows
    $parentMenu = [
        'guardar_encargo_zona' => 'modificar_encargos',
        'eliminar_encargo_zona' => 'modificar_encargos',
        'guardar_encargo_centro' => 'modificar_encargos_centros',
        'eliminar_encargo_centro' => 'modificar_encargos_centros',
        'cuadricula' => 'modificar_plan_de_misas',
        'update_iniciales' => 'modificar_iniciales_sacd_zona',
        'ver_cuadricula_zona' => 'modificar_plan_de_misas',
        'importar_plantilla' => 'modificar_plantilla',
        'horario_tarea' => 'modificar_plantilla',
        'anadir_ctr_tarea' => 'modificar_plantilla',
        'quitar_horario' => 'modificar_plantilla',
        'desplegable_encargos' => 'modificar_encargos_centros',
        'desplegable_sacd' => 'modificar_cuadricula_zona',
        'zona_sacd_datos' => null,
        'zona_sacd_datos_put' => null,
        'ver_plan_ctr' => 'buscar_plan_ctr',
        'ver_plan_sacd' => 'buscar_plan_sacd',
        'buscar_plan_ctr' => 'buscar_plan_ctr',
        'buscar_plan_sacd' => 'buscar_plan_sacd',
        'plan_de_misas_pantalla' => 'preparar_plan_de_misas',
        'crear_nuevo_periodo' => 'preparar_plan_de_misas',
        'cambiar_status' => 'cambiar_status',
        'nuevo_status' => 'cambiar_status',
        'ver_misas_zona' => 'ver_misas_zona',
        'ver_iniciales_zona' => 'modificar_iniciales_sacd_zona',
        'ver_encargos_zona' => 'modificar_encargos',
        'ver_encargos_centros' => 'modificar_encargos_centros',
        'modificar_encargos' => 'modificar_encargos',
        'modificar_encargos_centros' => 'modificar_encargos_centros',
        'modificar_plantilla' => 'modificar_plantilla',
        'modificar_iniciales_sacd_zona' => 'modificar_iniciales_sacd_zona',
    ];
    if ($menu === null && isset($parentMenu[$name])) {
        $parent = $parentMenu[$name];
        $menu = $parent ? ($menuByController[$parent] ?? null) : null;
    }

    $title = 'Flujo - ' . str_replace('_', ' ', ucwords($name));

    // preserve sections from body
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
