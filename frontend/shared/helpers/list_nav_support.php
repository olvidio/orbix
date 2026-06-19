<?php

use frontend\shared\web\Posicion;

/**
 * Navegación con {@see Posicion} tras `FrontBootstrap::boot()`.
 *
 * Guía completa: `documentacion/posicion_nav_post_frontbootstrap.md`
 *
 * Orden canónico en un controlador con `recordar()`:
 *   1. `FrontBootstrap::boot()` + `require list_nav_support.php`
 *   2. Leer POST / restaurar `stack` (ANTES de `recordar()`)
 *   3. `list_nav_clear_inherited_stack_for_recordar()` o `list_nav_boot_*_recordar()`
 *   4. `$oPosicion->recordar($refresh)` — guarda en sesión el POST completo (`$this->aParametros`)
 *   5. Opcional: actualizar solo `id_sel`/`scroll_id` con `setParametros` / `list_nav_persist_selection_*`
 *   6. NO sustituir la entrada con `replaceStackParametros` salvo formularios hijos que
 *      contaminan al padre (p. ej. volver a un dossier desde un form con otro hash de formulario)
 *
 * Al pulsar atrás, `Posicion::mostrar_left_slide()` llama a `HashFront::add_hash()`, que ignora
 * meta-hash antiguo (`h`, `hh`, …) y recalcula la firma (`hpos=1`). Por eso `recordar()` debe
 * conservar el POST de negocio completo, no un subconjunto “limpio” inventado.
 *
 * Audit: `php scripts/audit_posicion_nav_migration.php --strict`
 */
function list_nav_scroll_id_from_post(): string
{
    foreach ($_POST as $k => $v) {
        if (!is_string($k) || !str_starts_with($k, 'scroll_id_')) {
            continue;
        }
        if (!is_scalar($v)) {
            continue;
        }
        $s = (string) $v;
        if ($s !== '' && $s !== '0') {
            return $s;
        }
    }

    $raw = filter_input(INPUT_POST, 'scroll_id');

    return is_scalar($raw) ? (string) $raw : '';
}

/**
 * @return list<string|int>
 */
function list_nav_sel_from_post(): array
{
    $raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($raw)) {
        $out = [];
        foreach ($raw as $item) {
            if ($item === false) {
                continue;
            }
            $s = (string) $item;
            if ($s !== '') {
                $out[] = $s;
            }
        }
        if ($out !== []) {
            return $out;
        }
    }

    $fromIdSel = list_nav_id_sel_for_lista($_POST['id_sel'] ?? null);
    if (list_nav_id_sel_is_empty($fromIdSel)) {
        return [];
    }

    return is_array($fromIdSel) ? $fromIdSel : [$fromIdSel];
}

/**
 * Normaliza id_sel (Posicion o POST) al formato que acepta Lista (`select`).
 *
 * @return string|list<string>
 */
function list_nav_id_sel_for_lista(mixed $raw): string|array
{
    if (is_array($raw)) {
        $out = [];
        foreach ($raw as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $s = (string) $item;
            if ($s !== '') {
                $out[] = $s;
            }
        }
        if ($out === []) {
            return '';
        }

        return count($out) === 1 ? $out[0] : $out;
    }
    if (is_string($raw) && $raw !== '') {
        return $raw;
    }
    if (is_int($raw) || is_float($raw)) {
        return (string) $raw;
    }

    return '';
}

/**
 * @param string|list<string> $sel
 */
function list_nav_id_sel_is_empty(string|array $sel): bool
{
    return $sel === '' || $sel === [];
}

/**
 * @return array<int|string, array<string, mixed>>|null
 */
function list_nav_session_position(): ?array
{
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        return null;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = [];
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            return null;
        }
        $position[$key] = $entry;
    }

    return $position;
}

/**
 * @return array<string, mixed>|null
 */
function list_nav_session_stack_entry(int|string $index): ?array
{
    $position = list_nav_session_position();
    if ($position === null || !array_key_exists($index, $position)) {
        return null;
    }

    return $position[$index];
}

/**
 * @return string|list<string>
 */
function list_nav_normalize_id_sel(mixed $raw): string|array
{
    $normalized = list_nav_id_sel_for_lista($raw);

    return list_nav_id_sel_is_empty($normalized) ? '' : $normalized;
}

/**
 * Lee id_sel del POST (`id_sel`, `id_sel[]` o `sel[]`).
 *
 * @return string|list<string>
 */
function list_nav_id_sel_from_post(): string|array
{
    $postIdSel = $_POST['id_sel'] ?? null;
    if ($postIdSel !== null) {
        $normalized = list_nav_id_sel_for_lista($postIdSel);
        if (!list_nav_id_sel_is_empty($normalized)) {
            return $normalized;
        }
    }

    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        return list_nav_id_sel_for_lista($aSel);
    }

    return '';
}

/**
 * Persiste selección/scroll de un listado en la entrada anterior de la pila (p. ej. al abrir un detalle).
 */
function list_nav_persist_selection_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    $aSel = list_nav_sel_from_post();
    $scrollId = list_nav_scroll_id_from_post();
    if ($aSel === [] && $scrollId === '') {
        return;
    }

    $persist = [];
    if ($aSel !== []) {
        $persist['id_sel'] = count($aSel) === 1 ? $aSel[0] : $aSel;
    }
    if ($scrollId !== '') {
        $persist['scroll_id'] = $scrollId;
    }
    $oPosicion->setParametros($persist, $n);
}

/**
 * Persiste id_sel/scroll_id en la entrada de la pila del listado actual.
 *
 * @param string|list<string> $idSel
 */
function list_nav_persist_selection_on_list_page(Posicion $oPosicion, string|array $idSel, string $scrollId = '', bool $returningViaStack = false): void
{
    if (list_nav_id_sel_is_empty($idSel) && $scrollId === '') {
        return;
    }

    $persist = [];
    if (!list_nav_id_sel_is_empty($idSel)) {
        $persist['id_sel'] = $idSel;
    }
    if ($scrollId !== '') {
        $persist['scroll_id'] = $scrollId;
    }

    // Tras recordar(): n=0 guarda en la entrada actual; al volver con stack, en la que restauramos.
    $oPosicion->setParametros($persist, $returningViaStack ? 1 : 0);
}

/**
 * Añade restored_id_sel/restored_scroll_id al payload de una llamada interna al volver por pila.
 *
 * `id_sel` viaja en el POST del navegador pero no se firma server-to-server
 * ({@see \frontend\shared\security\HashFront::stripPostCamposUiDinamicos}); el backend del dossier
 * restaura la fila con `restored_id_sel` cuando hay `stack`.
 *
 * @param array<string, mixed> $apiPayload
 */
/**
 * Parámetros mínimos para recargar `dossiers_ver` al volver desde un formulario hijo (sin meta-hash).
 *
 * @return array<string, mixed>
 */
function list_nav_build_dossier_return_parametros(): array
{
    $parametros = [];
    foreach (['pau', 'obj_pau', 'id_dossier', 'queSel', 'que', 'permiso', 'bloque', 'clase_info'] as $key) {
        if (!isset($_POST[$key]) || !is_scalar($_POST[$key])) {
            continue;
        }
        $s = (string) $_POST[$key];
        if ($s !== '') {
            $parametros[$key] = $s;
        }
    }

    $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
    $idPau = is_int($idPauRaw) ? $idPauRaw : 0;
    if ($idPau <= 0) {
        $aSel = list_nav_sel_from_post();
        if ($aSel !== []) {
            $idPau = (int) strtok((string) $aSel[0], '#');
        }
    }
    if ($idPau > 0) {
        $parametros['id_pau'] = $idPau;
    }
    if (!isset($parametros['pau']) && $idPau > 0) {
        $parametros['pau'] = 'a';
    }

    $idSel = list_nav_id_sel_from_post();
    if (!list_nav_id_sel_is_empty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    $scrollId = list_nav_scroll_id_from_post();
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

/**
 * Parámetros para recargar `dossiers_ver` al persistir la entrada actual de la pila (n=0).
 *
 * @return array<string, mixed>
 */
function list_nav_build_dossiers_ver_stack_parametros(): array
{
    $parametros = list_nav_build_dossier_return_parametros();
    foreach (['mod', 'id_activ', 'depende', 'refresh'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }

    return $parametros;
}

/**
 * El padre inmediato de la pila (n=1) es `dossiers_ver`.
 */
function list_nav_stack_parent_is_dossiers_ver(int $n = 1): bool
{
    $url = list_nav_stack_entry_url($n);

    return $url !== '' && str_contains($url, 'dossiers_ver.php');
}

/**
 * Alguna entrada de la pila apunta a `dossiers_ver`.
 */
function list_nav_stack_has_dossiers_ver(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return false;
    }
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            session_write_close();

            return true;
        }
    }
    session_write_close();

    return false;
}

/**
 * El tope actual de la pila (n=0) es `dossiers_ver`.
 */
function list_nav_stack_top_is_dossiers_ver(): bool
{
    return list_nav_stack_parent_is_dossiers_ver(0);
}

/**
 * @return array<string, mixed>|null Entrada de pila más reciente con URL `dossiers_ver.php`
 */
function list_nav_find_best_dossiers_stack_entry(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return null;
    }
    $best = null;
    $bestKey = -1;
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $intKey = is_int($key) ? $key : (int) $key;
        if ($intKey >= $bestKey) {
            $bestKey = $intKey;
            $best = $entry;
        }
    }
    session_write_close();

    return $best;
}

/**
 * @return int Índice de la entrada `dossiers_ver` más reciente en la pila, o -1
 */
function list_nav_find_best_dossiers_stack_key(): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return -1;
    }
    $bestKey = -1;
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            continue;
        }
        $intKey = is_int($key) ? $key : (int) $key;
        if ($intKey >= $bestKey) {
            $bestKey = $intKey;
        }
    }
    session_write_close();

    return $bestKey;
}

/**
 * @return list<string>
 */
function list_nav_dossier_child_form_url_fragments(): array
{
    return [
        'form_cargos_de_actividad',
        'form_cargos_personas_en_actividad',
        'form_asistentes_a_una_actividad',
        'form_actividades_de_una_persona',
        'form_asignaturas_de_una_actividad',
        'form_matriculas_de_una_persona',
        'form_matriculas_de_una_actividad',
        'form_notas_de_una_persona',
        'acta_notas.php',
    ];
}

function list_nav_is_dossier_child_form_url(string $url): bool
{
    foreach (list_nav_dossier_child_form_url_fragments() as $fragment) {
        if (str_contains($url, $fragment)) {
            return true;
        }
    }

    return false;
}

function list_nav_is_dossier_shell_or_child_url(string $url): bool
{
    return str_contains($url, 'dossiers_ver.php') || list_nav_is_dossier_child_form_url($url);
}

function list_nav_reindex_position_stack(): void
{
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        return;
    }
    /** @var list<array<string, mixed>> $position */
    $position = array_values($_SESSION['position']);
    foreach ($position as $key => $values) {
        $position[$key]['stack'] = $key;
    }
    $_SESSION['position'] = $position;
}

/**
 * Quita formularios hijos del dossier (cargo, asistentes, …) que no deben ser destino de la flecha lateral.
 */
function list_nav_purge_dossier_child_forms_from_stack(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var list<array<string, mixed>> $kept */
    $kept = [];
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if ($url !== '' && list_nav_is_dossier_child_form_url($url)) {
            continue;
        }
        $kept[] = $entry;
    }
    $_SESSION['position'] = $kept;
    list_nav_reindex_position_stack();
    session_write_close();
}

/**
 * Quita `dossiers_ver` y formularios hijo del historial (al volver a un listado externo).
 */
function list_nav_purge_dossier_navigation_from_stack(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var list<array<string, mixed>> $kept */
    $kept = [];
    foreach ($_SESSION['position'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if ($url !== '' && list_nav_is_dossier_shell_or_child_url($url)) {
            continue;
        }
        $kept[] = $entry;
    }
    $_SESSION['position'] = $kept;
    list_nav_reindex_position_stack();
    session_write_close();
}

/**
 * @return int Índice de la entrada cuya URL contiene `$needle`, o -1
 */
function list_nav_find_stack_key_by_url_contains(string $needle): int
{
    if ($needle === '') {
        return -1;
    }
    $wasActive = session_status() === PHP_SESSION_ACTIVE;
    if (!$wasActive) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        if (!$wasActive) {
            session_write_close();
        }

        return -1;
    }
    $bestKey = -1;
    foreach ($_SESSION['position'] as $key => $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if ($url !== '' && str_contains($url, $needle)) {
            $intKey = is_int($key) ? $key : (int) $key;
            if ($intKey >= $bestKey) {
                $bestKey = $intKey;
            }
        }
    }
    if (!$wasActive) {
        session_write_close();
    }

    return $bestKey;
}

function list_nav_stack_from_post(): int
{
    $raw = filter_input(INPUT_POST, 'stack', FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : 0;
}

function list_nav_is_returning_via_stack(): bool
{
    return list_nav_stack_from_post() > 0;
}

/**
 * Actualiza la entrada `$stackIndex` con el POST actual y recorta todo lo posterior (atrás = no hay adelante).
 */
function list_nav_refresh_stack_entry_at_index(Posicion $oPosicion, int $stackIndex, string $bloque = '#main'): bool
{
    if ($stackIndex < 0) {
        return false;
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return false;
    }
    /** @var list<array<string, mixed>> $position */
    $position = array_values($_SESSION['position']);
    if (!array_key_exists($stackIndex, $position)) {
        session_write_close();

        return false;
    }
    $position = array_slice($position, 0, $stackIndex + 1);

    $parametros = list_nav_build_return_parametros_from_post();
    $parametros['stack'] = $stackIndex;

    $phpSelf = $_SERVER['PHP_SELF'] ?? '';
    $urlFromPosition = $position[$stackIndex]['url'] ?? null;
    $urlFallback = is_string($urlFromPosition) ? $urlFromPosition : '';
    $url = is_string($phpSelf) && $phpSelf !== '' ? $phpSelf : $urlFallback;

    $position[$stackIndex]['url'] = $url;
    $position[$stackIndex]['bloque'] = $bloque;
    $position[$stackIndex]['parametros'] = $parametros;
    $position[$stackIndex]['stack'] = $stackIndex;
    $_SESSION['position'] = $position;
    list_nav_reindex_position_stack();
    session_write_close();

    return $oPosicion->goStack($stackIndex);
}

/**
 * Tras `olvidar($stack)` al volver a un listado: purga dossier del historial y fija el tope sin `recordar()` append.
 */
function list_nav_boot_list_page_after_stack_return(Posicion $oPosicion, int $stackFromPost): void
{
    list_nav_purge_dossier_navigation_from_stack();

    $index = $stackFromPost;
    $phpSelf = $_SERVER['PHP_SELF'] ?? '';
    $needle = is_string($phpSelf) && $phpSelf !== '' ? basename($phpSelf) : '';

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (list_nav_session_stack_entry($index) === null) {
        $found = $needle !== '' ? list_nav_find_stack_key_by_url_contains($needle) : -1;
        if ($found >= 0) {
            $index = $found;
        }
    }
    session_write_close();

    if ($index >= 0 && list_nav_refresh_stack_entry_at_index($oPosicion, $index)) {
        return;
    }

    list_nav_boot_recordar($oPosicion);
}

/**
 * El tope actual de la pila (n=0) es un formulario hijo de dossier (cargo, asistentes, …).
 */
function list_nav_stack_top_is_dossier_child_form(): bool
{
    $url = list_nav_stack_entry_url(0);

    return $url !== '' && list_nav_is_dossier_child_form_url($url);
}

/**
 * Recorta la pila por delante del slot `dossiers_ver` indicado (o el más reciente si falla el índice).
 */
function list_nav_olvidar_forward_from_dossiers_slot(int $preferredIndex): void
{
    $index = $preferredIndex;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $entry = $index > 0 ? list_nav_session_stack_entry($index) : null;
    if ($entry !== null) {
        $url = isset($entry['url']) && is_string($entry['url']) ? $entry['url'] : '';
        if (!str_contains($url, 'dossiers_ver.php')) {
            $index = -1;
        }
    } else {
        $index = -1;
    }
    if ($index < 0) {
        $index = list_nav_find_best_dossiers_stack_key();
    }
    session_write_close();
    if ($index < 0) {
        return;
    }
    $oPosicion = new Posicion();
    if ($oPosicion->goStack($index)) {
        $oPosicion->olvidar($index);
    }
}

/**
 * Pasos hacia atrás desde el tope del dossier hasta un listado externo (p. ej. actividad_select),
 * saltando dossiers duplicados y formularios hijo en sub-bloque.
 */
function list_nav_back_steps_to_list_parent_from_dossiers(int $max = 15): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return 1;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = $_SESSION['position'];
    $stack = $position;
    end($stack);
    $steps = 0;
    while ($steps < $max) {
        if (prev($stack) === false) {
            break;
        }
        $steps++;
        $raw = current($stack);
        if (!is_array($raw)) {
            continue;
        }
        $url = isset($raw['url']) && is_string($raw['url']) ? $raw['url'] : '';
        if ($url !== '' && !list_nav_is_dossier_shell_or_child_url($url)) {
            session_write_close();

            return max(1, $steps);
        }
    }
    session_write_close();

    return 1;
}

function list_nav_mostrar_left_slide_to_list_parent_from_dossiers(Posicion $oPosicion): string
{
    return $oPosicion->mostrar_left_slide(list_nav_back_steps_to_list_parent_from_dossiers());
}

function list_nav_dossiers_ver_default_url(): string
{
    $phpSelf = $_SERVER['PHP_SELF'] ?? '';

    return is_string($phpSelf) && str_contains($phpSelf, 'dossiers_ver.php')
        ? $phpSelf
        : 'frontend/dossiers/controller/dossiers_ver.php';
}

/**
 * Pasos hacia atrás desde el tope hasta la entrada `dossiers_ver` más cercana (mín. 1).
 */
function list_nav_back_steps_to_dossiers_parent(int $max = 10): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return 1;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = $_SESSION['position'];
    $stack = $position;
    end($stack);
    $steps = 0;
    while ($steps < $max) {
        if (prev($stack) === false) {
            break;
        }
        $steps++;
        $raw = current($stack);
        if (!is_array($raw)) {
            continue;
        }
        $url = isset($raw['url']) && is_string($raw['url']) ? $raw['url'] : '';
        if (str_contains($url, 'dossiers_ver.php')) {
            session_write_close();

            return $steps;
        }
    }
    session_write_close();

    return 1;
}

function list_nav_mostrar_left_slide_to_dossiers_parent(Posicion $oPosicion): string
{
    return $oPosicion->mostrar_left_slide(list_nav_back_steps_to_dossiers_parent());
}

function list_nav_js_atras_to_dossiers_parent(Posicion $oPosicion): string
{
    return $oPosicion->js_atras(list_nav_back_steps_to_dossiers_parent());
}

function list_nav_go_atras_to_dossiers_parent(Posicion $oPosicion): string
{
    return $oPosicion->go_atras(list_nav_back_steps_to_dossiers_parent());
}

/**
 * Persiste id_sel/scroll_id en el dossier padre (n=1), nunca en actividad_select u otro listado.
 */
function list_nav_persist_dossier_parent_selection_if_dossier(Posicion $oPosicion): void
{
    $steps = list_nav_back_steps_to_dossiers_parent();
    if ($steps < 1 || !list_nav_stack_parent_is_dossiers_ver($steps)) {
        return;
    }
    list_nav_persist_selection_to_posicion($oPosicion, $steps);
}

/**
 * Si el tope no es `dossiers_ver`, inserta una entrada antes del hijo (p. ej. form en sub-bloque).
 */
function list_nav_ensure_dossiers_on_stack_before_child(): void
{
    if (list_nav_stack_top_is_dossiers_ver()) {
        return;
    }
    $existing = list_nav_find_best_dossiers_stack_entry();
    if ($existing !== null) {
        $parametros = isset($existing['parametros']) && is_array($existing['parametros'])
            ? $existing['parametros']
            : [];
        foreach (array_merge(list_nav_meta_hash_post_keys(), list_nav_stack_ephemeral_post_keys()) as $key) {
            unset($parametros[$key]);
        }
        $bloque = '#main';
        $dossiersUrl = isset($existing['url']) && is_string($existing['url'])
            ? $existing['url']
            : list_nav_dossiers_ver_default_url();
    } else {
        $parametros = list_nav_build_return_parametros_from_post();
        $idDossierRaw = $parametros['id_dossier'] ?? null;
        $idDossier = is_string($idDossierRaw)
            ? trim($idDossierRaw)
            : (is_scalar($idDossierRaw) ? trim((string) $idDossierRaw) : '');
        if ($idDossier === '') {
            return;
        }
        $bloque = '#main';
        $dossiersUrl = list_nav_dossiers_ver_default_url();
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        $_SESSION['position'] = [];
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    end($position);
    $topKey = key($position);
    $base = ($topKey === null || $topKey === '') ? 0 : (int) $topKey;
    $newKey = $base + 1;
    $parametros['stack'] = $newKey;
    $position[$newKey] = [
        'url' => $dossiersUrl,
        'bloque' => $bloque,
        'parametros' => $parametros,
        'stack' => $newKey,
    ];
    session_write_close();
}

function list_nav_parar_recordar_for_dossiers_refresh(int $Qrefresh): int
{
    if ($Qrefresh <= 0) {
        return 0;
    }

    return list_nav_stack_top_is_dossiers_ver() ? 1 : 0;
}

/**
 * Form o pantalla hija abierta desde un segmento de `dossiers_ver` (sub-bloque).
 *
 * - Asegura entrada `dossiers_ver` en la pila antes del hijo
 * - `recordar()` con POST completo (sin sustituir por POST mínimo)
 * - Solo actualiza selección en el padre si es `dossiers_ver`
 */
function list_nav_boot_dossier_child_recordar(Posicion $oPosicion, int $parar = 0): void
{
    list_nav_ensure_dossiers_on_stack_before_child();
    list_nav_boot_recordar($oPosicion, $parar);
    list_nav_persist_dossier_parent_selection_if_dossier($oPosicion);
}

/**
 * @deprecated Usar {@see list_nav_boot_dossier_child_recordar}. No sustituye el POST del dossier padre.
 */
function list_nav_persist_dossier_return_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    if (!list_nav_stack_parent_is_dossiers_ver($n)) {
        return;
    }
    // recordar() en dossiers_ver ya guardó el POST completo; no reemplazar por un subconjunto.
}

/**
 * Fusiona parámetros de dossier con campos extra de la pantalla actual.
 *
 * @param array<string, mixed> $extra
 * @return array<string, mixed>
 */
function list_nav_merge_dossier_return(array $extra): array
{
    return array_merge(list_nav_build_dossier_return_parametros(), $extra);
}

/**
 * Meta-hash de {@see HashFront} que no deben persistir en la pila de Posicion.
 *
 * @return list<string>
 */
function list_nav_meta_hash_post_keys(): array
{
    return ['h', 'hh', 'hhc', 'horig', 'hhorig', 'hc', 'hchk', 'hno', 'hnov'];
}

/**
 * Campos de navegación efímeros (no forman parte del estado de la pantalla al volver).
 *
 * @return list<string>
 */
function list_nav_stack_ephemeral_post_keys(): array
{
    return ['stack', 'Gstack'];
}

/**
 * Copia el POST actual sin meta-hash ni campos efímeros de navegación.
 *
 * @param array<string, mixed>|null $override Mezcla / sustituye claves tras la limpieza.
 * @return array<string, mixed>
 */
function list_nav_build_return_parametros_from_post(?array $override = null): array
{
    $parametros = $_POST;
    foreach (array_merge(list_nav_meta_hash_post_keys(), list_nav_stack_ephemeral_post_keys()) as $key) {
        unset($parametros[$key]);
    }
    if ($override !== null) {
        $parametros = array_merge($parametros, $override);
    }

    return $parametros;
}

/**
 * @param array<string, mixed> $parametros
 * @param string|list<string>|null $idSel
 * @return array<string, mixed>
 */
function list_nav_merge_selection_into_return_parametros(array $parametros, string|array|null $idSel = '', string $scrollId = ''): array
{
    $idSel = $idSel ?? '';
    if (!list_nav_id_sel_is_empty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

/**
 * Variante para `list_nav_persist_recordar_entry`: normaliza id_sel/scroll_id del POST.
 *
 * @param array<string, mixed> $parametros
 * @return array<string, mixed>
 */
function list_nav_merge_selection_for_recordar(array $parametros, mixed $idSel = null, mixed $scrollId = ''): array
{
    $normalizedIdSel = $idSel === null ? '' : list_nav_normalize_id_sel($idSel);

    return list_nav_merge_selection_into_return_parametros(
        $parametros,
        list_nav_id_sel_is_empty($normalizedIdSel) ? null : $normalizedIdSel,
        is_scalar($scrollId) ? (string) $scrollId : '',
    );
}

/**
 * Normaliza la entrada de pila que acaba de crear {@see Posicion::recordar()} (n=0).
 *
 * @param array<string, mixed> $parametros
 */
function list_nav_persist_recordar_entry(Posicion $oPosicion, array $parametros): void
{
    list_nav_persist_clean_return_to_posicion($oPosicion, $parametros, 0);
}

/**
 * Evita que recordar() herede `stack` del POST y haga deleteFroward sobre entradas intermedias.
 */
function list_nav_clear_inherited_stack_for_recordar(Posicion $oPosicion): void
{
    $oPosicion->setParametro('stack', 0);
}

/**
 * `recordar()` estándar post-FrontBootstrap: evita que el `stack` del POST trunque la pila.
 */
function list_nav_boot_recordar(Posicion $oPosicion, int $parar = 0): void
{
    list_nav_clear_inherited_stack_for_recordar($oPosicion);
    $oPosicion->recordar($parar);
}

/**
 * Al volver por pila a una pantalla ya recordada: actualiza su entrada sin append (evita duplicar dossier/cargo).
 */
function list_nav_refresh_stack_entry_on_return(Posicion $oPosicion, int $stackIndex): void
{
    if (list_nav_refresh_stack_entry_at_index($oPosicion, $stackIndex)) {
        return;
    }
    $fallback = list_nav_find_best_dossiers_stack_key();
    if ($fallback >= 0) {
        list_nav_refresh_stack_entry_at_index($oPosicion, $fallback);
    }
}

/**
 * Hijo de un listado que envía `Gstack` en el POST (p. ej. botones de `actividad_select`).
 * Re-graba la entrada del listado padre y luego `recordar()` sin heredar `stack`.
 */
function list_nav_boot_child_from_list_recordar(Posicion $oPosicion, int $parar = 0): void
{
    $gstack = filter_input(INPUT_POST, 'Gstack', FILTER_VALIDATE_INT);
    if (is_int($gstack) && $gstack > 0) {
        list_nav_repersist_stack_entry_from_gstack($gstack);
    }
    list_nav_boot_recordar($oPosicion, $parar);
}

/**
 * Actualiza la entrada anterior de la pila solo si su URL contiene el sufijo esperado.
 *
 * @param array<string, mixed> $parametros
 */
function list_nav_persist_parent_if_url(Posicion $oPosicion, array $parametros, string $urlMustContain): void
{
    if ($parametros === []) {
        return;
    }
    $parentUrl = list_nav_stack_entry_url(1);
    if ($parentUrl === '' || !str_contains($parentUrl, $urlMustContain)) {
        return;
    }
    $oPosicion->setParametros($parametros, 1);
}

/**
 * Parámetros para recargar `actividad_que` (formulario de búsqueda).
 *
 * @param array<string, mixed> $state
 * @return array<string, mixed>
 */
function list_nav_build_actividad_que_return_parametros(array $state): array
{
    $parametros = [];
    foreach ([
        'modo', 'que', 'status', 'id_tipo_activ', 'filtro_lugar', 'id_ubi', 'nom_activ',
        'periodo', 'year', 'dl_org', 'empiezamin', 'empiezamax',
        'fases_on', 'fases_off', 'publicado', 'listar_asistentes',
        'sasistentes', 'sactividad', 'sactividad2', 'extendida', 'ssfsv',
    ] as $key) {
        if (!array_key_exists($key, $state)) {
            continue;
        }
        $val = $state[$key];
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    $idSel = list_nav_normalize_id_sel($state['id_sel'] ?? null);
    $scrollIdRaw = $state['scroll_id'] ?? null;
    $scrollId = is_scalar($scrollIdRaw) ? (string) $scrollIdRaw : '';

    return list_nav_merge_selection_into_return_parametros($parametros, $idSel === '' ? null : $idSel, $scrollId);
}

/**
 * Parámetros para recargar `actividad_select` (listado de actividades).
 *
 * @param array<string, mixed> $state
 * @return array<string, mixed>
 */
function list_nav_build_actividad_select_return_parametros(array $state): array
{
    $parametros = list_nav_build_actividad_que_return_parametros($state);
    foreach (['ssfsv', 'sasistentes', 'sactividad', 'sactividad2'] as $extra) {
        if (!array_key_exists($extra, $state)) {
            continue;
        }
        $val = $state[$extra];
        if ($val === '' || $val === null) {
            continue;
        }
        $parametros[$extra] = $val;
    }

    return $parametros;
}

/**
 * Re-graba la entrada de pila indicada por `Gstack` (p. ej. actividad_select al abrir lista_asistentes).
 *
 * @param list<string> $paramKeys
 */
function list_nav_repersist_stack_entry_from_gstack(?int $gstackOverride = null, array $paramKeys = []): void
{
    $gstack = $gstackOverride ?? filter_input(INPUT_POST, 'Gstack', FILTER_VALIDATE_INT);
    if (!is_int($gstack) || $gstack === 0) {
        return;
    }

    $oRestore = new Posicion();
    if (!$oRestore->goStack($gstack)) {
        return;
    }

    if ($paramKeys === []) {
        $useActividadSelectReturn = true;
        $paramKeys = [
            'modo', 'que', 'status', 'id_tipo_activ', 'filtro_lugar', 'id_ubi', 'nom_activ',
            'periodo', 'year', 'dl_org', 'empiezamin', 'empiezamax',
            'fases_on', 'fases_off', 'publicado', 'listar_asistentes',
            'ssfsv', 'sasistentes', 'sactividad', 'sactividad2', 'extendida',
            'id_sel', 'scroll_id',
        ];
    } else {
        $useActividadSelectReturn = false;
    }

    $state = [];
    foreach ($paramKeys as $key) {
        $val = $oRestore->getParametro($key);
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $state[$key] = $val;
    }

    if ($state === []) {
        return;
    }

    $parametros = $useActividadSelectReturn
        ? list_nav_build_actividad_select_return_parametros($state)
        : $state;

    $parametros['stack'] = $gstack;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (list_nav_session_stack_entry($gstack) === null) {
        session_write_close();

        return;
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    if (!array_key_exists($gstack, $position)) {
        session_write_close();

        return;
    }
    $position[$gstack]['parametros'] = $parametros;
    if ($useActividadSelectReturn) {
        list_nav_rewrite_stack_entry_url($gstack, list_nav_actividad_select_controller_suffix());
    }
    session_write_close();
}

/**
 * Sufijo de URL del listado de actividades en la pila de {@see Posicion}.
 */
function list_nav_actividad_select_controller_suffix(): string
{
    return 'frontend/actividades/controller/actividad_select.php';
}

/**
 * Corrige la URL de una entrada de pila si apunta a otro controlador (p. ej. dossiers_ver).
 */
function list_nav_rewrite_stack_entry_url(int $gstack, string $requiredSuffix): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $entry = list_nav_session_stack_entry($gstack);
    if ($entry === null || !isset($entry['url']) || !is_string($entry['url'])) {
        session_write_close();

        return;
    }
    $current = $entry['url'];
    if (str_contains($current, $requiredSuffix)) {
        session_write_close();

        return;
    }
    $pos = strpos($current, 'frontend/');
    $newUrl = $pos !== false
        ? substr($current, 0, $pos) . $requiredSuffix
        : $requiredSuffix;
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position'])) {
        session_write_close();

        return;
    }
    /** @var array<int|string, array<string, mixed>> $position */
    $position = &$_SESSION['position'];
    if (!array_key_exists($gstack, $position)) {
        session_write_close();

        return;
    }
    $position[$gstack]['url'] = $newUrl;
    session_write_close();
}

/**
 * Parámetros de vuelta al formulario `actividad_que` en la entrada anterior de la pila.
 *
 * @param array<string, mixed> $parametros
 */
function list_nav_persist_actividad_que_parent(Posicion $oPosicion, array $parametros): void
{
    list_nav_persist_parent_if_url($oPosicion, $parametros, 'actividad_que.php');
}

/**
 * URL de una entrada de la pila relativa al tope (0 = actual, 1 = anterior, …).
 */
function list_nav_stack_entry_url(int $n = 0): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position']) || !is_array($_SESSION['position']) || $_SESSION['position'] === []) {
        session_write_close();

        return '';
    }
    $stack = $_SESSION['position'];
    end($stack);
    for ($i = 0; $i < $n; $i++) {
        if (prev($stack) === false) {
            reset($stack);
            break;
        }
    }
    $raw = current($stack);
    session_write_close();
    if (!is_array($raw) || !isset($raw['url']) || !is_string($raw['url'])) {
        return '';
    }

    return $raw['url'];
}

/**
 * Antes de {@see Posicion::recordar()} al abrir un hijo de `actividad_select` (POST trae `Gstack`).
 */
function list_nav_boot_actividad_select_child_recordar(Posicion $oPosicion, int $parar = 0): void
{
    list_nav_boot_child_from_list_recordar($oPosicion, $parar);
}

/**
 * Parámetros mínimos de la pantalla hija (sin campos de dossier del formulario `seleccionados`).
 *
 * @param array<string, mixed> $extra
 */
function list_nav_persist_actividad_select_child_entry(Posicion $oPosicion, array $extra = []): void
{
    $parametros = $extra;
    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }
    $parametros = list_nav_merge_selection_into_return_parametros(
        $parametros,
        list_nav_id_sel_from_post(),
        list_nav_scroll_id_from_post(),
    );
    foreach (['pau', 'obj_pau', 'queSel', 'id_dossier', 'permiso', 'Gstack', 'stack'] as $strip) {
        unset($parametros[$strip]);
    }
    list_nav_persist_recordar_entry($oPosicion, $parametros);
}

/**
 * Parámetros para recargar `dossiers_ver` abierto desde `actividad_select`.
 *
 * @return array<string, mixed>
 */
function list_nav_build_dossiers_ver_from_actividad_select_post(): array
{
    $parametros = [];
    foreach (['queSel', 'que', 'id_dossier', 'permiso', 'mod', 'pau', 'obj_pau'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }
    if (!isset($parametros['pau'])) {
        $parametros['pau'] = 'a';
    }
    if (!isset($parametros['obj_pau'])) {
        $parametros['obj_pau'] = 'Actividad';
    }
    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
        $first = $aSel[0];
        $idPau = (int) strtok((string) $first, '#');
        if ($idPau > 0) {
            $parametros['id_pau'] = $idPau;
        }
    }

    return list_nav_merge_selection_into_return_parametros(
        $parametros,
        list_nav_id_sel_from_post(),
        list_nav_scroll_id_from_post(),
    );
}

/**
 * `recordar()` al abrir dossiers desde el listado de actividades (`Gstack` en POST).
 */
function list_nav_boot_dossiers_from_actividad_select(Posicion $oPosicion, int $parar = 0): void
{
    list_nav_repersist_stack_entry_from_gstack();
    list_nav_boot_recordar($oPosicion, $parar);
    // No reemplazar parametros: recordar() ya guardó el POST completo del formulario.
}

/**
 * Sustituye parámetros de una entrada de la pila por un POST mínimo (sin meta-hash de formularios).
 *
 * @param array<string, mixed> $parametros
 */
function list_nav_persist_clean_return_to_posicion(Posicion $oPosicion, array $parametros, int $n = 0): void
{
    if ($parametros === []) {
        return;
    }
    $oPosicion->replaceStackParametros($parametros, $n);
}

/**
 * Restaura id_sel/scroll_id desde la pila cuando el POST trae `stack`. Llamar ANTES de recordar().
 *
 * @return array{id_sel: string|list<string>, scroll_id: string}
 */
function list_nav_restore_selection_from_stack_post(): array
{
    $result = ['id_sel' => '', 'scroll_id' => ''];
    $stackFromPost = isset($_POST['stack']) ? (int) filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : 0;
    if ($stackFromPost === 0) {
        return $result;
    }

    $oPosicionRestore = new Posicion();
    if (!$oPosicionRestore->goStack($stackFromPost)) {
        return $result;
    }

    $restoredSel = list_nav_id_sel_for_lista($oPosicionRestore->getParametro('id_sel'));
    if (!list_nav_id_sel_is_empty($restoredSel)) {
        $result['id_sel'] = $restoredSel;
    }
    $restoredScroll = $oPosicionRestore->getParametro('scroll_id');
    if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
        $result['scroll_id'] = (string) $restoredScroll;
    }
    $oPosicionRestore->olvidar($stackFromPost);

    return $result;
}

/**
 * Parámetros mínimos para recargar `personas_select` al volver desde un hijo.
 *
 * @return array<string, mixed>
 */
function list_nav_build_personas_select_return_parametros(): array
{
    $parametros = [];
    foreach (['que', 'exacto', 'cmb', 'nombre', 'apellido1', 'apellido2', 'centro', 'tabla', 'na', 'tipo'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }
    $esSacd = filter_input(INPUT_POST, 'es_sacd', FILTER_VALIDATE_INT);
    if (is_int($esSacd)) {
        $parametros['es_sacd'] = $esSacd;
    }
    $idSel = list_nav_id_sel_from_post();
    if (!list_nav_id_sel_is_empty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    $scrollId = list_nav_scroll_id_from_post();
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

/**
 * Parámetros mínimos para recargar `acta_select` al volver desde un hijo.
 *
 * @return array<string, mixed>
 */
function list_nav_build_acta_select_return_parametros(): array
{
    $parametros = [];
    foreach (['titulo', 'acta'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }
    $idSel = list_nav_id_sel_from_post();
    if (!list_nav_id_sel_is_empty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    $scrollId = list_nav_scroll_id_from_post();
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
}

/**
 * Parámetros mínimos de la pantalla anterior a `tessera_imprimir` / `tessera_ver`.
 *
 * @return array<string, mixed>
 */
function list_nav_build_tessera_return_parametros(): array
{
    $parametros = list_nav_build_personas_select_return_parametros();

    $idNom = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
    if (is_int($idNom) && $idNom > 0) {
        $parametros['id_nom'] = $idNom;
    }
    $idTabla = filter_input(INPUT_POST, 'id_tabla');
    if (is_scalar($idTabla) && (string) $idTabla !== '') {
        $parametros['id_tabla'] = (string) $idTabla;
    }
    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }

    return $parametros;
}

/**
 * Parámetros mínimos de la pantalla anterior a `certificado_emitido_imprimir`.
 *
 * @return array<string, mixed>
 */
function list_nav_build_certificado_imprimir_parent_return_parametros(): array
{
    $parametros = array_merge(
        list_nav_build_dossier_return_parametros(),
        list_nav_build_personas_select_return_parametros(),
    );
    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }
    $idNom = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
    if (is_int($idNom) && $idNom > 0) {
        $parametros['id_nom'] = $idNom;
    }

    return $parametros;
}

/**
 * Parámetros mínimos de la pantalla anterior a `e43`.
 *
 * @return array<string, mixed>
 */
function list_nav_build_e43_parent_return_parametros(): array
{
    $parametros = list_nav_build_dossier_return_parametros();
    $idPau = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
    if (is_int($idPau) && $idPau > 0) {
        $parametros['id_pau'] = $idPau;
    }
    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    }

    return $parametros;
}

/**
 * Parámetros mínimos para recargar `acta_notas` al volver desde un hijo (p. ej. `acta_imprimir`).
 *
 * @return array<string, mixed>
 */
function list_nav_build_acta_notas_return_parametros(): array
{
    $parametros = list_nav_build_dossier_return_parametros();

    $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
    if (is_int($idActivRaw) && $idActivRaw > 0) {
        $parametros['id_activ'] = $idActivRaw;
    }
    $idAsigRaw = filter_input(INPUT_POST, 'id_asignatura', FILTER_VALIDATE_INT);
    if (is_int($idAsigRaw) && $idAsigRaw > 0) {
        $parametros['id_asignatura'] = $idAsigRaw;
    }

    $aSel = list_nav_sel_from_post();
    if ($aSel !== []) {
        $parametros['sel'] = $aSel;
    } elseif (
        isset($parametros['id_activ'], $parametros['id_asignatura'])
        && is_int($parametros['id_activ'])
        && is_int($parametros['id_asignatura'])
    ) {
        $parametros['sel'] = [$parametros['id_activ'] . '#' . $parametros['id_asignatura']];
    }

    foreach (['opcional', 'primary_key_s', 'id_nivel'] as $key) {
        $raw = filter_input(INPUT_POST, $key);
        if (is_scalar($raw) && (string) $raw !== '') {
            $parametros[$key] = (string) $raw;
        }
    }

    return $parametros;
}

/**
 * Deja en la pila un POST limpio para volver a `acta_notas` (sin meta-hash de formularios anidados).
 */
function list_nav_persist_acta_notas_return_to_posicion(Posicion $oPosicion, int $n = 0): void
{
    $parametros = list_nav_build_acta_notas_return_parametros();
    if ($parametros === [] || !isset($parametros['id_activ'], $parametros['id_asignatura'])) {
        return;
    }
    list_nav_persist_clean_return_to_posicion($oPosicion, $parametros, $n);
}

/**
 * Parámetros mínimos de la pantalla anterior a `acta_imprimir` (`acta_notas` o `acta_select`).
 *
 * @return array<string, mixed>
 */
function list_nav_build_acta_imprimir_parent_return_parametros(): array
{
    $actaNotas = list_nav_build_acta_notas_return_parametros();
    if (isset($actaNotas['id_activ'], $actaNotas['id_asignatura'])) {
        return $actaNotas;
    }

    return list_nav_build_acta_select_return_parametros();
}

/**
 * Normaliza la entrada anterior de la pila antes de grabar `acta_imprimir` (flecha atrás).
 */
function list_nav_persist_acta_imprimir_parent_return_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_acta_imprimir_parent_return_parametros(),
        $n,
    );
}

/**
 * Normaliza la entrada anterior de la pila antes de grabar `tessera_imprimir` (flecha atrás).
 */
function list_nav_persist_tessera_imprimir_parent_return_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_tessera_return_parametros(),
        $n,
    );
}

/**
 * POST limpio para volver a `tessera_ver` / `personas_select` desde un hijo.
 */
function list_nav_persist_tessera_return_to_posicion(Posicion $oPosicion, int $n = 0): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_tessera_return_parametros(),
        $n,
    );
}

/**
 * Normaliza la entrada anterior de la pila antes de grabar `certificado_emitido_imprimir`.
 */
function list_nav_persist_certificado_imprimir_parent_return_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_certificado_imprimir_parent_return_parametros(),
        $n,
    );
}

/**
 * Normaliza la entrada anterior de la pila antes de grabar `e43`.
 */
function list_nav_persist_e43_parent_return_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_e43_parent_return_parametros(),
        $n,
    );
}

/**
 * POST limpio para volver a `acta_select` desde un hijo.
 */
function list_nav_persist_acta_select_return_to_posicion(Posicion $oPosicion, int $n = 0): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_acta_select_return_parametros(),
        $n,
    );
}

/**
 * POST limpio para volver a `personas_select` desde un hijo.
 */
function list_nav_persist_personas_select_return_to_posicion(Posicion $oPosicion, int $n = 0): void
{
    list_nav_persist_clean_return_to_posicion(
        $oPosicion,
        list_nav_build_personas_select_return_parametros(),
        $n,
    );
}

/**
 * @param array<string, mixed> $apiPayload
 */
function list_nav_apply_restored_selection_to_api_payload(
    array &$apiPayload,
    mixed $restoredIdSelFromStack = null,
    mixed $restoredScrollIdFromStack = null,
): void {
    $idSel = $restoredIdSelFromStack;
    if ($idSel === null || $idSel === '') {
        $idSel = list_nav_id_sel_from_post();
    } else {
        $idSel = list_nav_normalize_id_sel($idSel);
    }
    if (!list_nav_id_sel_is_empty($idSel)) {
        $apiPayload['restored_id_sel'] = $idSel;
    }

    $scroll = is_scalar($restoredScrollIdFromStack) ? (string) $restoredScrollIdFromStack : '';
    if ($scroll === '' || $scroll === '0') {
        $scroll = list_nav_scroll_id_from_post();
    }
    if ($scroll !== '' && $scroll !== '0') {
        $apiPayload['restored_scroll_id'] = $scroll;
    }
}
