<?php

/**
 * Aplica la revisión del módulo dossiers al catálogo (pantallas + flujos).
 * La capa API ya está revisada manualmente; este script no la toca.
 * Fuente menús: docs/guias/_referencia_menus.md
 *
 * Uso: php docs/scripts/aplicar_revision_dossiers.php
 */

declare(strict_types=1);

$repoRoot = dirname(__DIR__, 2);
$catalogo = $repoRoot . '/docs/catalogo/dossiers';
$menusFile = $repoRoot . '/docs/guias/_referencia_menus.md';

// --- Menús desde índice URL ---
$menuPermDossiers = [
    'legacy' => 'sistema > perm_dossiers > personas | ubis | actividades (según `tipo`)',
    'pills2' => 'ADMIN LOCAL > perm_dossiers > personas | ubis | actividades (según `tipo`)',
];

if (is_file($menusFile)) {
    $legacyParts = [];
    $pills2Parts = [];
    $lines = file($menusFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (!str_contains($line, 'frontend/dossiers/controller/perm_dossiers.php')) {
            continue;
        }
        if (!preg_match('/\|\s*base\s*\|\s*`[^`]+`\s*\|[^|]*\|[^|]*\|([^|]+)\|([^|]+)\|/', $line, $m)) {
            continue;
        }
        $legacyParts[] = trim($m[1]);
        $pills2Raw = trim(preg_replace('/<br>/', "\n", $m[2]));
        $pills2Pick = $pills2Raw;
        foreach (preg_split('/\n+/', $pills2Raw) as $part) {
            $part = trim($part);
            if (str_starts_with($part, 'ADMIN LOCAL >')) {
                $pills2Pick = $part;
                break;
            }
        }
        $pills2Parts[] = $pills2Pick;
    }
    if ($legacyParts !== []) {
        $menuPermDossiers['legacy'] = implode(' · ', array_unique($legacyParts));
    }
    if ($pills2Parts !== []) {
        $menuPermDossiers['pills2'] = implode(' · ', array_unique($pills2Parts));
    }
}

$pantallaResumen = [
    'dossiers_ver' => 'Visor de dossiers de una entidad (persona/actividad/ubi): cabecera con enlaces «dossiers» y «home», modo lista de carpetas o modo ficha con segmentos `select_*` y tablas `datos_tabla`. Gestiona navegación con `ListNavSupport` y firma `link_spec` en el frontend (`HashFront`).',
    'lista_dossiers' => 'Tabla parcial «relación de dossiers» (modo lista de `dossiers_ver`): icono y descripción por tipo; enlaces `href_ver` según permiso (`perm_a` 1 sin acceso, 2 lectura, 3 escritura). Renderizada desde `dossiers_ver.php`, no tiene controller propio.',
    'perm_dossiers' => 'Listado de tipos de dossier para administrar permisos de un ámbito (`tipo` = `p` personas, `u` ubis, `a` actividades). Cada fila enlaza a `perm_dossier_ver` para ver o modificar la definición del tipo.',
    'perm_dossier_ver' => 'Formulario de permisos de acceso a un tipo de dossier: metadatos (descripción, tablas, app/class/código), checkbox `depende_modificar` y máscaras de lectura/escritura por oficina. Guardar/eliminar solo con `admin_sv`/`admin_sf` (`perm_admin`).',
];

$pantallaSubtipo = [
    'dossiers_ver' => 'fragmento_ajax',
    'lista_dossiers' => 'fragmento_ajax',
    'perm_dossiers' => 'pantalla_principal',
    'perm_dossier_ver' => 'fragmento_ajax',
];

$pantallaController = [
    'lista_dossiers' => 'frontend/dossiers/controller/dossiers_ver.php',
    'dossiers_ver' => 'frontend/dossiers/controller/dossiers_ver.php',
    'perm_dossiers' => 'frontend/dossiers/controller/perm_dossiers.php',
    'perm_dossier_ver' => 'frontend/dossiers/controller/perm_dossier_ver.php',
];

$pantallaMenu = [
    'perm_dossiers' => $menuPermDossiers,
    'perm_dossier_ver' => $menuPermDossiers,
    'dossiers_ver' => null,
    'lista_dossiers' => null,
];

$flujoObjetivo = [
    'dossiers_ver_pantalla' => 'Abrir y navegar los dossiers de una persona, actividad o ubi: cabecera, relación de carpetas o ficha con widgets embebidos (matrículas, asistentes, certificados, tablas genéricas). Reutilizado desde `home_persona`, `home_ubis`, `actividad_ver` y otras pantallas vía `fnjs_update_div`.',
    'dossiers_lista_fichas' => 'Mostrar la tabla de carpetas de dossiers disponibles para la entidad actual, con iconos de permiso y enlace a cada ficha (`href_ver` firmado en frontend).',
    'perm_dossiers' => 'Elegir el ámbito de tipos de dossier (personas/ubis/actividades) y abrir la edición de permisos de cada tipo desde el menú de administración.',
    'perm_dossier_ver' => 'Consultar o modificar la definición y máscaras de permiso de un `TipoDossier` concreto; volver al listado tras guardar o eliminar.',
    'tipo_dossier' => 'Persistir cambios (`tipo_dossier_guardar`) o eliminar (`tipo_dossier_eliminar`) un tipo de dossier desde el formulario `perm_dossier_ver` (solo administradores `admin_sv`/`admin_sf`).',
];

$flujoParentMenu = [
    'perm_dossiers' => 'perm_dossiers',
    'perm_dossier_ver' => 'perm_dossiers',
    'tipo_dossier' => 'perm_dossiers',
    'dossiers_ver_pantalla' => null,
    'dossiers_lista_fichas' => null,
];

$flujoErrores = [
    'dossiers_ver_pantalla' => [
        'clase_info invalida',
        'No encuentro a nadie con id_nom: <id>',
        'ubi no encontrada',
        'actividad no encontrada',
        'pau desconocido',
        'El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).',
    ],
    'dossiers_lista_fichas' => [],
    'perm_dossiers' => [],
    'perm_dossier_ver' => ['No se encuentra el dossier: <id>'],
    'tipo_dossier' => [
        'falta id_tipo_dossier',
        'No se encuentra el dossier: <id>',
        'Hay un error, no se ha guardado.',
        'Hay un error, no se ha eliminado.',
    ],
];

$flujoEscenarios = [
    'dossiers_ver_pantalla' => <<<'MD'
## Escenarios

### Abrir relación de dossiers

1. Desde el home de persona/ubi o la cabecera de actividad, pulsar el icono/enlace de dossiers.
2. El controller carga `dossiers_ver_pantalla_data` con `pau` + `id_pau` (y opcionalmente `obj_pau`).
3. Si `id_dossier` está vacío, se muestra la cabecera (`dossiers_ver_top`) y la tabla `lista_dossiers`.
4. Cada fila con permiso abre la ficha vía `fnjs_update_div` y `href_ver`.

### Ver o editar una ficha de dossier

1. Pulsar una carpeta con permiso de lectura/escritura.
2. El backend devuelve `modo=ficha` y `ficha_segmentos` (`select_*` o `datos_tabla`).
3. El frontend renderiza cada segmento (widgets de otros módulos o tabla genérica `DossiersVerFichaDatosTabla`).
4. La cabecera permite volver a la relación (`go_dossiers`) o al home del sujeto (`go_home`).

### Reutilización desde otras vistas (`queSel`)

1. Pantallas de asistentes, matrículas, cargos, etc. invocan `dossiers_ver` con `queSel`/`que` (`activ`, `matriculas`, `asis`, `asig`, `carg`).
2. El caso de uso fuerza `pau`, `permiso` e `id_dossier` según el contexto.

MD,
    'dossiers_lista_fichas' => <<<'MD'
## Escenarios

### Listar carpetas disponibles

1. Con `id_dossier` vacío, `dossiers_ver` solicita filas a `dossiers_lista_fichas_data`.
2. El frontend firma `href_ver`/`href_abrir` con `DossiersListaSupport::signFilas`.
3. La vista pinta icono, descripción y símbolo de permiso (deny/eye/pencil).

MD,
    'perm_dossiers' => <<<'MD'
## Escenarios

### Elegir ámbito y tipo de dossier

1. Menú `perm_dossiers` con `tipo=p|u|a` (personas, ubis o actividades).
2. `perm_dossiers_data` devuelve `a_filas` con `pagina_link_spec` hacia `perm_dossier_ver`.
3. Pulsar «ver o modificar permisos» en una fila carga el formulario del tipo.

MD,
    'perm_dossier_ver' => <<<'MD'
## Escenarios

### Consultar permisos de un tipo

1. Llega `tipo` + `id_tipo_dossier` desde el listado.
2. `perm_dossier_ver_data` devuelve metadatos, máscaras y `permiso_dossier_bit_map`.
3. Si el usuario no es `admin_sv`/`admin_sf`, el formulario es solo lectura (`botones=0`).

### Guardar o eliminar (admin)

1. Con `perm_admin`, aparecen botones guardar/eliminar.
2. Guardar: POST `tipo_dossier_guardar` con campos del formulario y arrays `Permiso_lectura[]`/`Permiso_escritura[]`.
3. Eliminar: confirmación con `txt_eliminar`, POST `tipo_dossier_eliminar`, vuelta al listado con `go_to`.

MD,
    'tipo_dossier' => <<<'MD'
## Escenarios

### Guardar cambios

1. Admin pulsa «guardar cambios» → `fnjs_guardar` serializa `#frm2` y POST a `tipo_dossier_guardar`.
2. Éxito: `success: true`, `data: "ok"`. Error: alert con `mensaje`.

### Eliminar tipo

1. Admin pulsa «eliminar» → confirmación → POST `tipo_dossier_eliminar`.
2. Éxito: recarga listado `perm_dossiers` vía `fnjs_update_div` y `go_to`.

MD,
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
        'dossiers_ver' => 'Dossiers Ver',
        'lista_dossiers' => 'Lista Dossiers',
        'perm_dossiers' => 'Perm Dossiers',
        'perm_dossier_ver' => 'Perm Dossier Ver',
        'dossiers_ver_pantalla' => 'Dossiers Ver Pantalla',
        'dossiers_lista_fichas' => 'Dossiers Lista Fichas',
        'tipo_dossier' => 'Tipo Dossier',
    ];
    return $map[$name] ?? ucwords(str_replace('_', ' ', $name));
}

// ========== PANTALLAS ==========
$pantCount = 0;
foreach (glob($catalogo . '/pantallas/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $resumen = $pantallaResumen[$name] ?? 'Pantalla del módulo dossiers.';
    $subtipo = $pantallaSubtipo[$name] ?? 'fragmento_ajax';
    $controller = $pantallaController[$name] ?? "frontend/dossiers/controller/{$name}.php";
    $menu = $pantallaMenu[$name] ?? null;

    $fm = setFmField($fm, 'subtipo', '"' . $subtipo . '"');
    $fm = setFmField($fm, 'estado_revision', '"revisado"');
    if ($name === 'lista_dossiers') {
        $fm = setFmField($fm, 'controller', '"' . $controller . '"');
    }

    $sections = [];
    foreach (['Vistas Relacionadas', 'Fragmentos Frontend Relacionados', 'Endpoints Usados', 'Capacidades Relacionadas', 'Campos Detectados', 'Acciones Detectadas'] as $sec) {
        if (preg_match('/## ' . preg_quote($sec, '/') . '\n(.*?)(?=\n## |\z)/s', $body, $m)) {
            $sections[$sec] = "## {$sec}\n" . $m[1];
        }
    }

    $title = titleFromName($name);
    $newBody = "# {$title}\n\n{$resumen}\n\n";
    $newBody .= "## Tipo\n\n- Subtipo: `{$subtipo}`\n";
    $newBody .= "- Controller: `{$controller}`\n\n";
    foreach ($sections as $s) {
        $newBody .= rtrim($s) . "\n\n";
    }
    $newBody .= "## Manual De Usuario\n\nPantalla revisada contra `frontend/dossiers/` y `src/dossiers/`.\n\n";
    $newBody .= menuSection($menu);
    $newBody = rtrim($newBody) . "\n";

    file_put_contents($path, "---\n$fm\n---\n\n$newBody");
    $pantCount++;
}

// ========== FLUJOS ==========
$flujCount = 0;
foreach (glob($catalogo . '/flujos/*.md') as $path) {
    $name = basename($path, '.md');
    $content = (string) file_get_contents($path);
    [$fm, $body] = parseFrontMatter($content);

    $fm = setFmField($fm, 'estado_revision', '"revisado"');

    $objetivo = $flujoObjetivo[$name] ?? "Flujo de usuario del módulo dossiers (`{$name}`).";
    $parent = $flujoParentMenu[$name] ?? null;
    $menu = $parent ? ($pantallaMenu[$parent] ?? null) : null;

    $puntoEntrada = $menu
        ? "Menú Legacy: {$menu['legacy']}. Pills2: {$menu['pills2']}."
        : 'Sin entrada de menú directa; acceso embebido desde home persona/ubi, actividad u otras pantallas que enlazan `dossiers_ver.php`.';

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
    $newBody .= $flujoEscenarios[$name] ?? '';
    if (isset($flujoEscenarios[$name])) {
        $newBody .= "\n";
    }

    foreach ($keepSections as $s) {
        $newBody .= rtrim($s) . "\n\n";
    }

    $errores = $flujoErrores[$name] ?? [];
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

echo "✅ Pantallas: $pantCount | Flujos: $flujCount (API sin cambios)\n";
