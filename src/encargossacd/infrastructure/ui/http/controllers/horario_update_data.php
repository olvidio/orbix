<?php

use src\encargossacd\application\EncargoHorarioUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$mod = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'mod');
try {
    if ($mod === 'eliminar') {
        $ctx = HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
            'horario_update_data'
        );
        $id_item_h = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item_h');
        $_POST['sel_nom'] = [(string) $id_item_h];
    } else {
        $ctx = HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
            'horario_update_data'
        );
        $_POST['id_enc'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc');
        $_POST['id_item_h'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item_h');
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), []);
    return;
}

/** @var EncargoHorarioUpdate $useCase */
$useCase = DependencyResolver::get(EncargoHorarioUpdate::class);


$result = $useCase->ejecutar($_POST);
if (isset($result['_error'])) {
    ContestarJson::enviar($result['_error'], []);
    return;
}

ContestarJson::enviar('', ['ok' => true]);
