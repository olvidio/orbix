<?php
/**
 * Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;


$input = [
    'id_item_usuario_objeto' => input_int($_POST, 'id_item_usuario_objeto'),
    'id_usuario' => input_int($_POST, 'id_usuario'),
    'id_tipo_activ' => input_string($_POST, 'id_tipo_activ'),
    'dl_propia' => input_string($_POST, 'dl_propia'),
    'objeto' => input_string($_POST, 'objeto'),
    'aviso_tipo' => input_int($_POST, 'aviso_tipo'),
    'id_fase_ref' => input_int($_POST, 'id_fase_ref'),
    'aviso_off' => input_string($_POST, 'aviso_off'),
    'aviso_on' => input_string($_POST, 'aviso_on'),
    'aviso_outdate' => input_string($_POST, 'aviso_outdate'),
    'casas' => input_string_list($_POST, 'casas'),
];

/** @var CambioUsuarioObjetoPrefGuardar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefGuardar::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
