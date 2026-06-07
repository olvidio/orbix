<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_item_egm = input_int($_POST, 'id_item_egm');
$error_txt = '';

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
/** @var WhereisRepositoryInterface $WhereisRepository */
$WhereisRepository = DependencyResolver::get(WhereisRepositoryInterface::class);
/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
$oEgm = $EgmRepository->findById($Qid_item_egm);
if ($oEgm === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}
$id_lugar = $oEgm->getId_lugar();
$oLugar = $LugarRepository->findById((int) $id_lugar);
if ($oLugar === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}
$nombre_valija = $oLugar->getNom_lugar();

$cWhereis = $WhereisRepository->getWhereare(['id_item_egm' => $Qid_item_egm]);

$d = 0;
$a_valores = [];
$a_tipo = [];
foreach ($cWhereis as $oWhereis) {
    $d++;
    $id_item_whereis = $oWhereis->getId_item_whereis();
    $id_doc = $oWhereis->getId_doc();
    $oDocumento = $DocumentoRepository->findById((int) $id_doc);
    if ($oDocumento === null) {
        continue;
    }
    //extract($oDocumento->getTot());
    $identificador = $oDocumento->getIdentificador();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
    if ($oTipoDoc === null) {
        continue;
    }
    $id_lugar_doc = $oDocumento->getId_lugar();
    $lugar = '';
    if ($id_lugar_doc !== null) {
        $oLugar = $LugarRepository->findById($id_lugar_doc);
        if ($oLugar === null) {
            continue;
        }
        $lugar = $oLugar->getNom_lugar();
    }
    //$identificador = _("de") . " $lugar: $identificador";
    $identificador = $identificador;

    $a_valores[$d]['sel'] = $id_item_whereis;
    $a_valores[$d][1] = $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc();
    $a_valores[$d][2] = $identificador;
    //para poder ordenar
    $a_tipo[$d] = $a_valores[$d][1];
}
// ordenar por sigla
if (!empty($a_valores)) {
    array_multisort($a_tipo, SORT_ASC, $a_valores);
}

$data = [
    'a_valores' => $a_valores,
    'nombre_valija' => $nombre_valija,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
