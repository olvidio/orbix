<?php


/**
 * Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'cambio_usuario_objeto_pref_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item_usuario_objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item_usuario_objeto'),
    'id_usuario' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_usuario'),
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

$idConfirmado = (int)($result['id_item_usuario_objeto'] ?? 0);
if ($error === '' && $idConfirmado > 0) {
    $result['ctx_guardar_propiedades'] = HashB::sign(
        'cambio_usuario_propiedad_pref_guardar_todas',
        ['id_item_usuario_objeto' => $idConfirmado]
    );
}

ContestarJson::enviar($error, $result);
