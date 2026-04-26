<?php
/**
 * Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.
 *
 * Sucesor de la rama `guardar_objeto` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$a_casas = (array)filter_input(INPUT_POST, 'casas', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_casas = array_filter($a_casas, static fn($v) => $v !== null && $v !== '');

$input = [
    'id_item_usuario_objeto' => (int)filter_input(INPUT_POST, 'id_item_usuario_objeto'),
    'id_usuario' => (int)filter_input(INPUT_POST, 'id_usuario'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'dl_propia' => (string)filter_input(INPUT_POST, 'dl_propia'),
    'objeto' => (string)filter_input(INPUT_POST, 'objeto'),
    'aviso_tipo' => (int)filter_input(INPUT_POST, 'aviso_tipo'),
    'id_fase_ref' => (int)filter_input(INPUT_POST, 'id_fase_ref'),
    'aviso_off' => (string)filter_input(INPUT_POST, 'aviso_off'),
    'aviso_on' => (string)filter_input(INPUT_POST, 'aviso_on'),
    'aviso_outdate' => (string)filter_input(INPUT_POST, 'aviso_outdate'),
    'casas' => $a_casas,
];

$result = CambioUsuarioObjetoPrefGuardar::execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
