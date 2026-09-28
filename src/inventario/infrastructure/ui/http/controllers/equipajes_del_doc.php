<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Whereis;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_del'),
        'equipajes_del_doc'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

// grabar el pack (equipaje-grupo-lugar) + grabar los docs seleccionados
/** @var WhereisRepositoryInterface $WhereisRepository */
$WhereisRepository = DependencyResolver::get(WhereisRepositoryInterface::class);
foreach ($a_sel as $id_item_whereis_raw) {
    if (!is_numeric($id_item_whereis_raw)) {
        continue;
    }
    $id_item_whereis = (int) $id_item_whereis_raw;
    $oWhereis = new Whereis();
    $oWhereis->setId_item_whereis($id_item_whereis);

    if ($WhereisRepository->Eliminar($oWhereis) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $WhereisRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');
