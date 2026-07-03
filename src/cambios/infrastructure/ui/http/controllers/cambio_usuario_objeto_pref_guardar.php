<?php


/**
 * Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item_usuario_objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item_usuario_objeto'),
    'id_usuario' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_usuario'),
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'dl_propia' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl_propia'),
    'objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'objeto'),
    'aviso_tipo' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'aviso_tipo'),
    'id_fase_ref' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_fase_ref'),
    'aviso_off' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'aviso_off'),
    'aviso_on' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'aviso_on'),
    'aviso_outdate' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'aviso_outdate'),
    'casas' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'casas'),
];

/** @var CambioUsuarioObjetoPrefGuardar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefGuardar::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
