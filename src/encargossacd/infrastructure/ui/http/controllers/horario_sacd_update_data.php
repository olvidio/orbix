<?php

use src\encargossacd\application\EncargoSacdHorarioUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$mod = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'mod');
try {
    if ($mod === 'eliminar') {
        $ctx = HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
            'horario_sacd_update_data'
        );
        $_POST['id_item'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
    } else {
        $ctx = HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
            'horario_sacd_update_data'
        );
        $_POST['id_nom'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
        $_POST['id_enc'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc');
        $_POST['id_item'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), []);
    return;
}

/** @var EncargoSacdHorarioUpdate $useCase */
$useCase = DependencyResolver::get(EncargoSacdHorarioUpdate::class);


$result = $useCase->ejecutar($_POST);
if (isset($result['_error'])) {
    ContestarJson::enviar($result['_error'], []);
    return;
}

ContestarJson::enviar('', ['ok' => true]);
