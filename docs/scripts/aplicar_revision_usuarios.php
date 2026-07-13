<?php

/**
 * Aplica la revisión del módulo usuarios al catálogo (API + pantallas + flujos).
 * Fuente API: docs/catalogo/usuarios/api/_metadata_review.json
 *
 * Uso: php docs/scripts/aplicar_revision_usuarios.php
 */

declare(strict_types=1);

$repoRoot = dirname(__DIR__, 2);
$catalogo = $repoRoot . '/docs/catalogo/usuarios';
$metaFile = $catalogo . '/api/_metadata_review.json';
$menusFile = $repoRoot . '/docs/guias/_referencia_menus.md';

if (!is_file($metaFile)) {
    fwrite(STDERR, "Falta $metaFile\n");
    exit(1);
}

/** @var array<string, array<string, mixed>> $meta */
$meta = json_decode((string) file_get_contents($metaFile), true, 512, JSON_THROW_ON_ERROR);

// Menús (hardcoded + opcional índice _referencia_menus.md)
$menuByController = [
    'usuario_lista' => [
        'legacy' => 'sistema > usuarios web > lista usuarios',
        'pills2' => 'ADMIN LOCAL > usuarios web > lista usuarios',
    ],
    'role_lista' => [
        'legacy' => 'sistema > usuarios web > lista de roles',
        'pills2' => 'ADMIN LOCAL > usuarios web > lista de roles',
    ],
    'grupo_lista' => [
        'legacy' => 'sistema > usuarios web > grupos',
        'pills2' => 'ADMIN LOCAL > usuarios web > grupos',
    ],
    'preferencias' => [
        'legacy' => 'menú usuario > preferencias',
        'pills2' => 'menú usuario > preferencias',
    ],
];

if (is_file($menusFile)) {
    $lines = file($menusFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (!preg_match('/\|\s*usuarios\s*\|\s*`([^`]+)`\s*\|[^|]*\|[^|]*\|([^|]+)\|([^|]+)\|/', $line, $m)) {
            continue;
        }
        $url = trim($m[1]);
        if (!str_contains($url, 'frontend/usuarios/controller/')) {
            continue;
        }
        $legacy = trim(preg_replace('/<br>.*/', '', trim(preg_replace('/<br>/', "\n", $m[2]))));
        $pills2 = trim(preg_replace('/<br>.*/', '', trim($m[3])));
        $basename = basename($url, '.php');
        $menuByController[$basename] = ['legacy' => $legacy, 'pills2' => $pills2];
    }
}

$pantallaResumen = [
    'usuario_lista' => 'Listado principal de usuarios web con filtro por login, alta/edición y borrado.',
    'usuario_form' => 'Ficha usuario: datos, rol, pau, permisos menú/actividad y grupos (admin id_role≤3).',
    'usuario_form_mail' => 'Formulario cambio de email del usuario autenticado.',
    'usuario_form_pwd' => 'Formulario cambio de contraseña con validación de fortaleza.',
    'usuario_form_2fa' => 'Configuración 2FA: activar/desactivar TOTP con verificación previa.',
    'usuario_reset_2fa' => 'Fragmento admin para resetear 2FA de un usuario.',
    'usuario_grupo_lst' => 'Tabla AJAX de grupos disponibles para asignar al usuario.',
    'usuario_grupo_del_lst' => 'Tabla AJAX de grupos ya asignados al usuario.',
    'role_lista' => 'Listado de roles con grupmenus asociados; CRUD según permiso superadmin/admin.',
    'role_form' => 'Alta/edición de rol (sf/sv/pau/dmz) y tabla grupmenus asignados.',
    'role_grupmenu' => 'Pantalla asignación grupmenu↔rol (añadir desde candidatos).',
    'grupo_lista' => 'Listado de grupos de permisos (id ~ ^5) con alta/edición/borrado.',
    'grupo_form' => 'Formulario alta/edición de grupo de permisos.',
    'perm_menu_form' => 'Modal permiso menú DL (bits oficina/grupo).',
    'perm_activ_lista' => 'Pestaña permisos actividad-proceso en ficha usuario.',
    'preferencias' => 'Preferencias personales: layout, inicio, idioma, tablas, estilo.',
    'login' => 'Pantalla login web (HTML, no JSON).',
    'recovery' => 'Dispatcher recuperación acceso (password/2FA/ayuda).',
    'recuperar_password' => 'Formulario solicitud recuperación contraseña por email.',
    'recuperar_2fa' => 'Formulario solicitud recuperación código 2FA.',
    'ayuda_acceso' => 'Ayuda acceso: muestra email ofuscado y contacto admin.',
    'ayuda_2fa_reset' => 'Ayuda reset 2FA embebida en formulario 2FA.',
    'mails_contactos_region' => 'Listado contactos regionales para pantalla recovery.',
    'borrar_todos_pwd' => 'Herramienta pruebas: reset masivo contraseñas (solo entorno pruebas).',
];

$pantallaSubtipo = [
    'usuario_lista' => 'pantalla_principal',
    'role_lista' => 'pantalla_principal',
    'grupo_lista' => 'pantalla_principal',
    'preferencias' => 'pantalla_principal',
    'login' => 'pantalla_principal',
    'recovery' => 'pantalla_principal',
    'recuperar_password' => 'pantalla_principal',
    'recuperar_2fa' => 'pantalla_principal',
    'usuario_form' => 'fragmento_ajax',
    'usuario_form_mail' => 'fragmento_ajax',
    'usuario_form_pwd' => 'fragmento_ajax',
    'usuario_form_2fa' => 'fragmento_ajax',
    'role_form' => 'fragmento_ajax',
    'role_grupmenu' => 'fragmento_ajax',
    'grupo_form' => 'fragmento_ajax',
    'perm_menu_form' => 'modal',
    'perm_activ_lista' => 'fragmento_ajax',
    'usuario_grupo_lst' => 'fragmento_ajax',
    'usuario_grupo_del_lst' => 'fragmento_ajax',
    'usuario_reset_2fa' => 'fragmento_ajax',
    'ayuda_acceso' => 'fragmento_ajax',
    'ayuda_2fa_reset' => 'fragmento_ajax',
    'mails_contactos_region' => 'fragmento_ajax',
    'borrar_todos_pwd' => 'fragmento_ajax',
];

$flujoObjetivo = [
    'usuario' => 'Administración de usuarios web: listar, alta/edición en ficha, borrado y asignación grupos/permisos.',
    'role' => 'Administración de roles: listar, crear/editar flags sf/sv/pau/dmz y asignar grupmenus.',
    'grupo' => 'Administración de grupos de permisos: listar, alta/edición y borrado.',
    'perm_menu' => 'Gestión permisos menú DL de un usuario desde su ficha.',
    'perm_activ' => 'Gestión permisos actividad-proceso de un usuario (módulo procesos).',
    'preferencias' => 'Ajuste preferencias personales: layout, inicio, idioma, tablas y estilo.',
    'app_login' => 'Autenticación app móvil con credenciales y 2FA opcional.',
    'usuario_2fa' => 'Configuración autenticación dos factores del usuario.',
    'usuario_preferencias' => 'Carga datos iniciales de la pantalla preferencias.',
];

$parentMenu = [
    'usuario' => 'usuario_lista',
    'usuario_info' => 'usuario_form',
    'usuario_guardar' => 'usuario_form',
    'usuario_eliminar' => 'usuario_lista',
    'usuario_grupo_add' => 'usuario_form',
    'usuario_grupo_del' => 'usuario_form',
    'usuario_grupo_lst' => 'usuario_form',
    'usuario_grupo_del_lst' => 'usuario_form',
    'perm_menu' => 'usuario_form',
    'perm_menu_info' => 'perm_menu_form',
    'perm_activ' => 'usuario_form',
    'usuario_guardar_mail' => 'preferencias',
    'usuario_guardar_pwd' => 'usuario_form_pwd',
    'usuario_check_pwd' => 'usuario_form_pwd',
    'usuario_2fa' => 'usuario_form_2fa',
    'usuario_2fa_info' => 'usuario_form_2fa',
    'usuario_2fa_verify' => 'usuario_form_2fa',
    'usuario_preferencias' => 'preferencias',
    'preferencia_tabla' => 'preferencias',
    'preferencias' => 'preferencias',
    'role' => 'role_lista',
    'role_info' => 'role_form',
    'role_grupmenu_add' => 'role_grupmenu',
    'role_grupmenu_del' => 'role_form',
    'role_grupmenu_info' => 'role_grupmenu',
    'grupo' => 'grupo_lista',
    'grupo_info' => 'grupo_form',
    'app_login' => 'login',
    'app_session' => 'login',
    'check_first_login_2fa' => 'login',
    'recuperar_password_mail' => 'recuperar_password',
    'recuperar_2fa_mail' => 'recuperar_2fa',
    'usuario_ayuda_info' => 'ayuda_acceso',
    'borrar_pwd' => 'borrar_todos_pwd',
    'mails_contactos_region' => 'recovery',
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
        if ($v === 'array' || $v === 'boolean') {
            $type = $v;
        }
        $parts[] = 'post.' . $k . ':' . $type;
    }
    return yamlList($parts);
}

function formatSalidaText(array $salida, string $operacion): string
{
    $lines = ["- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).", "- Forma: `standard_envelope_string_data`."];
    $success = $salida['success_data'] ?? null;
    if ($success === 'ok' || $success === 'redirect') {
        $lines[] = $success === 'redirect'
            ? '- Exito: redirección HTTP (no JSON); flujo post-login 2FA.'
            : '- Exito: `success: true`, `data: "ok"` (string vacío serializado).';
    } elseif (is_array($success) && $success === []) {
        $lines[] = '- Exito: `success: true`, `data: "{}"`.';
    } elseif ($operacion === 'lista_data' || $operacion === 'form_data') {
        $lines[] = '- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):';
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
    return ucwords(str_replace('_', ' ', $name));
}

$permDefault = 'Autorización vía id_role (≤3 admin, 1 superadmin, 2 admin circunscripción), HashB en mutaciones sensibles, frontend + `$_SESSION[\'oPerm\']`.';

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
    $casosList = $casos === []
        ? '- _(lógica inline en controller)_'
        : implode("\n", array_map(static fn($c) => "- `src\\usuarios\\application\\$c`", $casos));

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

- URL: `/src/usuarios/{$name}`
- Metodos registrados: `GET, POST`
- Operacion: `{$operacion}`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/{$name}.php`

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

    $resumen = $pantallaResumen[$name] ?? 'Pantalla del módulo usuarios (auth, preferencias, admin usuarios/roles/grupos).';
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
    $flujoEndpointMap['/src/usuarios/' . $endpoint] = $m;
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

    $objetivo = $flujoObjetivo[$name]
        ?? ($endpointMeta['objetivo_funcional'] ?? "Flujo de usuario del módulo usuarios (`{$name}`).");
    $errores = $endpointMeta['errores'] ?? [];

    $menu = null;
    if (preg_match('/fragmentos:\s*\["usuarios\.pantalla\.([^"]+)"\]/', $fm, $frag)) {
        $menu = $menuByController[$frag[1]] ?? null;
    }
    if (preg_match('/pantallas_principales:\s*\["usuarios\.pantalla\.([^"]+)"\]/', $fm, $pp)) {
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
        : 'Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.';

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
