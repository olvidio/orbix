<?php

use frontend\shared\web\Posicion;

/**
 * Lee scroll_id enviado por {@see frontend\shared\web\Lista} (`scroll_id_<tabla>`) o `scroll_id`.
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
 * Deja en la entrada anterior de la pila un POST limpio para volver al dossier (lista de asignaturas, etc.).
 */
function list_nav_persist_dossier_return_to_posicion(Posicion $oPosicion, int $n = 1): void
{
    list_nav_persist_clean_return_to_posicion($oPosicion, list_nav_build_dossier_return_parametros(), $n);
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
 * @param string|list<string> $idSel
 * @return array<string, mixed>
 */
function list_nav_merge_selection_into_return_parametros(array $parametros, string|array $idSel = '', string $scrollId = ''): array
{
    if (!list_nav_id_sel_is_empty($idSel)) {
        $parametros['id_sel'] = $idSel;
    }
    if ($scrollId !== '' && $scrollId !== '0') {
        $parametros['scroll_id'] = $scrollId;
    }

    return $parametros;
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

    $idSel = $state['id_sel'] ?? '';
    $scrollId = isset($state['scroll_id']) ? (string) $state['scroll_id'] : '';

    return list_nav_merge_selection_into_return_parametros($parametros, $idSel, $scrollId);
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
        $paramKeys = [
            'modo', 'que', 'status', 'id_tipo_activ', 'filtro_lugar', 'id_ubi', 'nom_activ',
            'periodo', 'year', 'dl_org', 'empiezamin', 'empiezamax',
            'fases_on', 'fases_off', 'publicado', 'listar_asistentes',
            'ssfsv', 'sasistentes', 'sactividad', 'sactividad2', 'extendida',
            'id_sel', 'scroll_id',
        ];
    }

    $parametros = [];
    foreach ($paramKeys as $key) {
        $val = $oRestore->getParametro($key);
        if ($val === '' || $val === null || $val === []) {
            continue;
        }
        $parametros[$key] = $val;
    }

    if ($parametros === []) {
        return;
    }

    $parametros['stack'] = $gstack;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['position'][$gstack]) || !is_array($_SESSION['position'][$gstack])) {
        session_write_close();

        return;
    }
    $_SESSION['position'][$gstack]['parametros'] = $parametros;
    session_write_close();
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

function list_nav_apply_restored_selection_to_api_payload(
    array &$apiPayload,
    mixed $restoredIdSelFromStack = null,
    mixed $restoredScrollIdFromStack = null,
): void {
    $idSel = $restoredIdSelFromStack;
    if ($idSel === null || $idSel === '') {
        $idSel = list_nav_id_sel_from_post();
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
