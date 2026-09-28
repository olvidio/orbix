<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar_grupo'),
        'equipajes_eliminar_grupo'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_grupo = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_grupo');
$Qid_equipaje = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_equipaje');

$error_txt = '';

// Nuevo egm:
/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
$aWhere = [
    'id_equipaje' => $Qid_equipaje,
    'id_grupo' => $Qid_grupo,
];
$cEgm = $EgmRepository->getEgmes($aWhere);
if (!empty($cEgm)) {
    $oEgm = $cEgm[0];
    if ($EgmRepository->Eliminar($oEgm) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $EgmRepository->getErrorTxt();
    }
    // los docs en whereis deberían eliminarse por la base de datos, al tener una foreign key.
}

ContestarJson::enviar($error_txt, 'ok');

