<?php
/**
 * Endpoint JSON: construye el texto de preview de la condicion y el array
 * serializado (cambio_prop) sin persistir nada.
 *
 * Sucesor de la rama `guardar_cond` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefPreview;
use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$a_id_ubi = (array)filter_input(INPUT_POST, 'id_ubi', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_id_ubi = array_filter($a_id_ubi, static fn($v) => $v !== null && $v !== '');

$input = [
    'id_item' => (int)filter_input(INPUT_POST, 'id_item'),
    'objeto' => (string)filter_input(INPUT_POST, 'objeto'),
    'propiedad' => (string)filter_input(INPUT_POST, 'propiedad'),
    'operador' => (string)filter_input(INPUT_POST, 'operador'),
    'valor' => (string)filter_input(INPUT_POST, 'valor'),
    'valor_old' => (string)filter_input(INPUT_POST, 'valor_old'),
    'valor_new' => (string)filter_input(INPUT_POST, 'valor_new'),
    'id_ubi' => $a_id_ubi,
];

$result = CambioUsuarioPropiedadPrefPreview::execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
