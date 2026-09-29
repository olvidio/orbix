<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeExcepcionGuardar;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'contribucion_no_duerme_excepcion_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$modo = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'modo');
if ($modo === 'existing') {
    $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_tipo_activ');
} else {
    $id_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');
}
$valor = (string)\src\shared\domain\helpers\FilterPostGet::post('valor');

/** @var ContribucionNoDuermeExcepcionGuardar $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeExcepcionGuardar::class);

$error_txt = $useCase->execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
