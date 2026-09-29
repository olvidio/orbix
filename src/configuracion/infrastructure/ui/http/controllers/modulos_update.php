<?php

use src\configuracion\application\ModulosUpdateAction;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

$ctxEliminar = (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar');
try {
    if ($ctxEliminar !== '') {
        $ctx = HashB::open($ctxEliminar, 'modulos_eliminar');
        $_POST['id_mod'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_mod');
        $_POST['mod'] = 'eliminar';
        unset($_POST['sel']);
    } else {
        $ctx = HashB::open(
            (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
            'modulos_guardar'
        );
        $_POST['id_mod'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_mod');
        $_POST['mod'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'mod');
    }
} catch (HashBInvalidException $e) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo _("Operación no autorizada");
    return;
}

/** @var ModulosUpdateAction $useCase */
$useCase = DependencyResolver::get(ModulosUpdateAction::class);

header('Content-Type: text/plain; charset=UTF-8');
echo $useCase->execute($_POST);
