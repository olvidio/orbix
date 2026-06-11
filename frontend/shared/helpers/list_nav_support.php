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
    $parametros = list_nav_build_dossier_return_parametros();
    if ($parametros === []) {
        return;
    }
    $oPosicion->replaceStackParametros($parametros, $n);
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
