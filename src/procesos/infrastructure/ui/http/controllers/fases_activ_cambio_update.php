<?php

use src\procesos\application\FasesActivCambioUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$selIn = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
$selRebuilt = [];
$idFase = '';
$accion = '';
try {
    foreach ($selIn as $capsule) {
        $ctx = HashB::open($capsule, 'fases_activ_cambio_update');
        $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
        if ($idActiv <= 0) {
            ContestarJson::enviar(_("Operación no autorizada"), 'none');
            return;
        }
        $selRebuilt[] = (string) $idActiv;
        $idFase = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_fase_nueva');
        $accion = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'accion');
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['sel'] = $selRebuilt;
$_POST['id_fase_nueva'] = $idFase;
$_POST['accion'] = $accion;

/** @var FasesActivCambioUpdate $useCase */
$useCase = DependencyResolver::get(FasesActivCambioUpdate::class);

ContestarJson::enviar($useCase->execute($_POST));
