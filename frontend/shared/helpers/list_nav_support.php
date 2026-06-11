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
