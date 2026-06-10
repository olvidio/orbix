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
    if (!is_array($raw)) {
        return [];
    }
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

    return $out;
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
        $persist['id_sel'] = $aSel;
    }
    if ($scrollId !== '') {
        $persist['scroll_id'] = $scrollId;
    }
    $oPosicion->setParametros($persist, $n);
}
