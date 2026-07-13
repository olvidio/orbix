<?php

/**
 * Aplica la revisión del módulo ubiscamas al catálogo (API + pantallas + flujos).
 * Fuente API: docs/catalogo/ubiscamas/api/_metadata_review.json
 *
 * Uso: php docs/scripts/aplicar_revision_ubiscamas.php
 */

declare(strict_types=1);

$repoRoot = dirname(__DIR__, 2);
$catalogo = $repoRoot . '/docs/catalogo/ubiscamas';
$metaFile = $catalogo . '/api/_metadata_review.json';

if (!is_file($metaFile)) {
    fwrite(STDERR, "Falta $metaFile\n");
    exit(1);
}

/** @var array<string, array<string, mixed>> $meta */
$meta = json_decode((string) file_get_contents($metaFile), true, 512, JSON_THROW_ON_ERROR);

$pantallaResumen = [
    'lista_habitaciones' => 'Asignación visual de asistentes a camas de una actividad (dossier actividad, enlace `camas`).',
    'lista_habitaciones_distribucion' => 'Vista tabular de distribución habitación/cama/asistente abierta desde lista_habitaciones.',
    'lista_habitaciones_nombres' => 'Listado alfabético de asistentes con habitación y planta asignadas.',
    'habitacion_form' => 'Formulario de alta/edición de habitación CDC con gestión inline de camas.',
    'cama_form' => 'Formulario modal para editar descripción, larga y VIP de una cama.',
];

$pantallaSubtipo = [
    'lista_habitaciones' => 'fragmento_ajax',
    'lista_habitaciones_distribucion' => 'fragmento_ajax',
    'lista_habitaciones_nombres' => 'fragmento_ajax',
    'habitacion_form' => 'fragmento_ajax',
    'cama_form' => 'modal',
];

$flujoPuntoEntrada = [
    'actividad_habitaciones' => 'Dossier de actividad, enlace `camas` (`frontend/actividades/view/actividades.js` → `lista_habitaciones.php`). Sin entrada de menú en el índice.',
    'habitacion' => 'Dossier CDC habitaciones (`select_habitaciones_cdc` en ficha ubi) o navegación desde formulario de habitación. Sin entrada de menú en el índice.',
    'cama' => 'Modal invocado desde `habitacion_form` (editar/nueva cama). Sin entrada de menú en el índice.',
    'update_cama_asistente' => 'Acción AJAX en `lista_habitaciones.phtml` (flechas asignar/desasignar cama). Sin entrada de menú en el índice.',
    'update_solo_vip' => 'Checkbox «solo VIP» en `lista_habitaciones.phtml`. Sin entrada de menú en el índice.',
];

$flujoObjetivo = [
    'actividad_habitaciones' => 'Listar camas de la ubi de una actividad, asignar o reasignar asistentes (drag-and-drop), activar modo solo VIP y abrir vistas de distribución o nombres.',
    'habitacion' => 'Dar de alta, modificar o eliminar habitaciones de un ubi CDC, incluyendo creación automática de camas según número indicado.',
    'cama' => 'Crear, editar o eliminar camas individuales asociadas a una habitación.',
    'update_cama_asistente' => 'Persistir la asignación cama↔asistente en la actividad actual (requiere token HashB).',
    'update_solo_vip' => 'Alternar el filtro de solo camas VIP en la actividad (`desc_activ=camasVIP`).',
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
        $type = match (true) {
            str_contains((string) $v, 'array') => 'array',
            str_contains((string) $v, 'integer') => 'integer',
            default => 'string',
        };
        $parts[] = 'post.' . $k . ':' . $type;
    }
    return yamlList($parts);
}

function formatSalidaText(array $m, string $operacion): string
{
    $respuesta = $m['respuesta'] ?? 'standard_envelope_string_data';
    $salida = $m['salida'] ?? [];

    if ($respuesta === 'raw_response') {
        $lines = ['- Helper: `echo json_encode` (JSON directo, sin sobre de `ContestarJson`).', '- Forma: `raw_response`.'];
        $success = $salida['success_data'] ?? [];
        $lines[] = '- Exito: `success: true`, `mensaje: "ok"`.';
        if (is_array($success)) {
            foreach ($success as $k => $v) {
                $lines[] = "  - `$k`: $v";
            }
        }
        return implode("\n", $lines);
    }

    $lines = [
        '- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).',
        '- Forma: `standard_envelope_string_data`.',
    ];
    $success = $salida['success_data'] ?? null;
    if ($success === 'ok' || $success === null) {
        $lines[] = '- Exito: `success: true`, `data: "ok"` (string vacío serializado en mutaciones).';
    } elseif ($operacion === 'lista_data' || $operacion === 'form_data') {
        $lines[] = '- Claves en `data` (doble `JSON.parse`):';
        foreach ($success as $k => $v) {
            $lines[] = "  - `$k`: $v";
        }
    } else {
        $lines[] = '- Exito: payload en `data`.';
    }
    return implode("\n", $lines);
}

function menuSection(): string
{
    return "## Ruta de menú\n\n- **Legacy:** sin entrada de menú en el índice\n- **Pills2:** sin entrada de menú en el índice\n";
}

function titleFromName(string $name): string
{
    return ucwords(str_replace('_', ' ', $name));
}

$permDefault = 'Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION[\'oPerm\']` y permisos del dossier/actividad padre.';

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
    $requiereHashb = !empty($m['requiere_hashb']);
    $respuesta = $m['respuesta'] ?? 'standard_envelope_string_data';

    $fm = setFmField($fm, 'operacion', '"' . $operacion . '"');
    $fm = setFmField($fm, 'entrada', formatEntradaFm($entrada));
    $fm = setFmField($fm, 'entrada_obligatoria', yamlList($oblig));
    $fm = setFmField($fm, 'errores', yamlList($errores));
    $fm = setFmField($fm, 'respuesta', '"' . $respuesta . '"');
    $fm = setFmField($fm, 'requiere_hashb', $requiereHashb ? 'true' : 'false');
    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $frontendRefs = $m['frontend_referencias'] ?? [];
    $fm = setFmField($fm, 'frontend_referencias', yamlList($frontendRefs));

    $casos = $m['casos_uso'] ?? [];
    if ($casos !== []) {
        $fm = setFmField($fm, 'casos_uso', yamlList(array_map(
            static fn($c) => 'src\\ubiscamas\\application\\' . $c,
            $casos
        )));
    }

    $title = titleFromName($name);

    $entradaRows = '';
    foreach ($entrada as $k => $v) {
        $ob = in_array($k, $oblig, true) ? 'Si' : 'No';
        $notas = '';
        if ($k === 'sel') {
            $notas = 'Token `id_habitacion#...` o `id_cama#...` según endpoint';
        }
        if ($k === 'ctx') {
            $notas = 'Cápsula HashB; `id_activ` se lee del contexto abierto';
        }
        if ($k === 'solo_vip') {
            $notas = 'String `"true"` activa modo VIP';
        }
        $entradaRows .= "| `$k` | `$v` | application | $ob | $notas |\n";
    }
    if ($entradaRows === '') {
        $entradaRows = "| _(ninguno)_ | | | | |\n";
    }

    $permisos = $m['permisos'] ?? [];
    $permText = $permisos === [] ? $permDefault : implode(' ', $permisos);

    $casosList = $casos === []
        ? '- Lógica inline en el controller (sin caso de uso en `application/`).'
        : implode("\n", array_map(static fn($c) => "- `src\\ubiscamas\\application\\$c`", $casos));

    $salidaText = formatSalidaText($m, $operacion);
    $objetivo = $m['objetivo_funcional'];

    $frontendList = $frontendRefs === []
        ? '- Sin referencias directas detectadas; revisar payloads `url_*` / `hash_*`.'
        : implode("\n", array_map(static fn($f) => "- `$f`", $frontendRefs));

    $newBody = <<<MD
# {$title}

{$objetivo}

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

{$objetivo}

## Endpoint

- URL: `/src/ubiscamas/{$name}`
- Metodos registrados: `GET, POST`
- Operacion: `{$operacion}`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/{$name}.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
{$entradaRows}
## Salida

{$salidaText}

## Errores conocidos

MD;
    if ($errores === []) {
        $newBody .= "\n- _(ninguno documentado)_\n";
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

{$frontendList}

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

    $resumen = $pantallaResumen[$name] ?? 'Pantalla del módulo ubiscamas (habitaciones y camas).';
    $subtipo = $pantallaSubtipo[$name] ?? 'fragmento_ajax';

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
    $newBody .= menuSection();
    $newBody = rtrim($newBody) . "\n";

    file_put_contents($path, "---\n$fm\n---\n\n$newBody");
    $pantCount++;
}

// ========== FLUJOS ==========
$flujoEndpointMap = [];
foreach ($meta as $endpoint => $m) {
    $flujoEndpointMap['/src/ubiscamas/' . $endpoint] = $m;
}

$flujCount = 0;
foreach (glob($catalogo . '/flujos/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $objetivo = $flujoObjetivo[$name] ?? "Flujo de usuario del módulo ubiscamas (`{$name}`).";
    $puntoEntrada = $flujoPuntoEntrada[$name] ?? 'Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.';

    $errores = [];
    preg_match('/endpoints:\s*(\[[^\]]*\])/', $fm, $epMatch);
    if (!empty($epMatch[1])) {
        preg_match_all('/"([^"]+)"/', $epMatch[1], $eps);
        foreach ($eps[1] ?? [] as $ep) {
            if (isset($flujoEndpointMap[$ep])) {
                $errores = array_merge($errores, $flujoEndpointMap[$ep]['errores'] ?? []);
            }
        }
    }
    $errores = array_values(array_unique($errores));

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
    $newBody .= menuSection();
    $newBody = rtrim($newBody) . "\n";

    file_put_contents($path, "---\n$fm\n---\n\n$newBody");
    $flujCount++;
}

echo "✅ API: $apiCount | Pantallas: $pantCount | Flujos: $flujCount\n";
