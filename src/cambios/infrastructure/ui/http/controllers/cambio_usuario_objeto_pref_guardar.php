<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item_usuario_objeto' => FuncTablasSupport::inputInt($_POST, 'id_item_usuario_objeto'),
    'id_usuario' => FuncTablasSupport::inputInt($_POST, 'id_usuario'),
    'id_tipo_activ' => FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'dl_propia' => FuncTablasSupport::inputString($_POST, 'dl_propia'),
    'objeto' => FuncTablasSupport::inputString($_POST, 'objeto'),
    'aviso_tipo' => FuncTablasSupport::inputInt($_POST, 'aviso_tipo'),
    'id_fase_ref' => FuncTablasSupport::inputInt($_POST, 'id_fase_ref'),
    'aviso_off' => FuncTablasSupport::inputString($_POST, 'aviso_off'),
    'aviso_on' => FuncTablasSupport::inputString($_POST, 'aviso_on'),
    'aviso_outdate' => FuncTablasSupport::inputString($_POST, 'aviso_outdate'),
    'casas' => FuncTablasSupport::inputStringList($_POST, 'casas'),
];

/** @var CambioUsuarioObjetoPrefGuardar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefGuardar::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
