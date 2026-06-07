<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosOpcionesData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

$filtro = [];

if (array_key_exists('active', $_POST)) {
    $filtro['active'] = is_true($_POST['active']);
}
if (array_key_exists('sv', $_POST)) {
    $filtro['sv'] = is_true($_POST['sv']);
}
if (array_key_exists('sf', $_POST)) {
    $filtro['sf'] = is_true($_POST['sf']);
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
if (!empty($_POST['tipo_ctr'])) {
    $filtro['tipo_ctr'] = input_string($_POST, 'tipo_ctr');
}

ContestarJson::enviar('', DependencyResolver::get(CentrosOpcionesData::class)->execute($filtro));
