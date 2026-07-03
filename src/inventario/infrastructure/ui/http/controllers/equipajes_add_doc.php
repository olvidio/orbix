<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Whereis;
use src\shared\web\ContestarJson;

$Qid_item_egm = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item_egm');
$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

// grabar el pack (equipaje-grupo-lugar) + grabar los docs seleccionados
/** @var WhereisRepositoryInterface $WhereisRepository */
$WhereisRepository = DependencyResolver::get(WhereisRepositoryInterface::class);
foreach ($a_sel as $id_doc_raw) {
    if (!is_numeric($id_doc_raw)) {
        continue;
    }
    $id_doc = (int) $id_doc_raw;
    $id_item_whereis = $WhereisRepository->getNewId();
    $oWhereis = new Whereis();
    $oWhereis->setId_item_whereis($id_item_whereis);
    $oWhereis->setId_item_egm($Qid_item_egm);
    $oWhereis->setId_doc($id_doc);

    if ($WhereisRepository->Guardar($oWhereis) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $WhereisRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');

