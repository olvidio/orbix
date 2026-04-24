<?php
/**
 * Endpoint backend: listado de avisos `CambioUsuario` (con `avisado=false`)
 * para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla
 * `avisos_generar`.
 */

use src\cambios\application\AvisosGenerarListaData;
use web\ContestarJson;

$input = [
    'id_usuario' => (int)filter_input(INPUT_POST, 'id_usuario'),
    'aviso_tipo' => (int)filter_input(INPUT_POST, 'aviso_tipo'),
];
$result = AvisosGenerarListaData::execute($input);

$error = $result['error'];
$data = [
    'a_valores' => $result['a_valores'],
    'aOpcionesUsuarios' => $result['aOpcionesUsuarios'],
    'aOpcionesAvisoTipo' => $result['aOpcionesAvisoTipo'],
];
ContestarJson::enviar($error, $data);
