<?php

use src\ubis\application\CasasOpcionesData;
use frontend\shared\web\ContestarJson;

$filtro = [];

if (array_key_exists('active', $_POST)) {
    $filtro['active'] = filter_var($_POST['active'], FILTER_VALIDATE_BOOLEAN);
}
if (array_key_exists('sv', $_POST)) {
    $filtro['sv'] = filter_var($_POST['sv'], FILTER_VALIDATE_BOOLEAN);
}
if (array_key_exists('sf', $_POST)) {
    $filtro['sf'] = filter_var($_POST['sf'], FILTER_VALIDATE_BOOLEAN);
}
if (!empty($_POST['id_ubi_in'])) {
    $raw = $_POST['id_ubi_in'];
    if (is_string($raw)) {
        $raw = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
    if (is_array($raw)) {
        $filtro['id_ubi_in'] = array_values(array_map('intval', $raw));
    }
}

ContestarJson::enviar('', CasasOpcionesData::execute($filtro));
