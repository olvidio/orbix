<?php
/**
 * Endpoint backend: listado de avisos `CambioUsuario` (con `avisado=false`)
 * para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla
 * `avisos_generar`.
 * {@see \frontend\cambios\helpers\AvisosGenerarListaRender} compone URLs y hash de borrado.
 */

use src\cambios\application\AvisosGenerarListaData;
use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'id_usuario' => (int)filter_input(INPUT_POST, 'id_usuario'),
    'aviso_tipo' => (int)filter_input(INPUT_POST, 'aviso_tipo'),
    'is_admin' => (int)filter_input(INPUT_POST, 'is_admin') === 1,
];
$result = AvisosGenerarListaData::execute($input);

$error = $result['error'];
unset($result['error']);
ContestarJson::enviar($error, $result);
