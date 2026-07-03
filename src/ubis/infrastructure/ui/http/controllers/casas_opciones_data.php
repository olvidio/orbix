<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CasasOpcionesData;
use src\shared\web\ContestarJson;

$filtro = [];

if (array_key_exists('active', $_POST)) {
    $filtro['active'] = \src\shared\domain\helpers\FuncTablasSupport::isTrue($_POST['active']);
}
if (array_key_exists('sv', $_POST)) {
    $filtro['sv'] = \src\shared\domain\helpers\FuncTablasSupport::isTrue($_POST['sv']);
}
if (array_key_exists('sf', $_POST)) {
    $filtro['sf'] = \src\shared\domain\helpers\FuncTablasSupport::isTrue($_POST['sf']);
}
if (!empty($_POST['id_ubi_in'])) {
    $raw = $_POST['id_ubi_in'];
    if (is_string($raw)) {
        $raw = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
    if (is_array($raw)) {
        $ids = [];
        foreach ($raw as $item) {
            if (!is_int($item) && !is_string($item) && !is_float($item) && !is_bool($item) && $item !== null) {
                continue;
            }
            $ids[] = (int) $item;
        }
        $filtro['id_ubi_in'] = $ids;
    }
}

ContestarJson::enviar('', DependencyResolver::get(CasasOpcionesData::class)->execute($filtro));
