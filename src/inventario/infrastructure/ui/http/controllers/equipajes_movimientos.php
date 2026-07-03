<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\shared\web\ContestarJson;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';
$aCambios = [];
$aLugaresPorEgm = [];
$aNomEquipajes = [];

/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
/** @var WhereisRepositoryInterface $WhereisRepository */
$WhereisRepository = DependencyResolver::get(WhereisRepositoryInterface::class);
/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
foreach ($a_sel as $id_equipaje) {
    $oEquipaje = $EquipajeRepository->findById((int) $id_equipaje);
    if ($oEquipaje === null) {
        continue;
    }
    $aGrupos[$id_equipaje]= $oEquipaje->getNom_equipaje();
    $aNomEquipajes[$id_equipaje]= $oEquipaje->getNom_equipaje();
    // tadas las maletas
    $cEgm = $EgmRepository->getEgmes(['id_equipaje'=>$id_equipaje]);
    $txt = '';
    foreach ($cEgm as $oEgm) {
        $id_item_egm = $oEgm->getId_item();
        $id_grupo = $oEgm->getId_grupo();
        $id_lugar = $oEgm->getId_lugar();
        $oLugar = $LugarRepository->findById((int) $id_lugar);
        if ($oLugar === null) {
            continue;
        }
        $aLugaresPorEgm[$id_item_egm] = $oLugar->getNom_lugar();
        // por grupo, comparar los de documentos y los de whereis
        // Quitar
        $cDocumentos = $DocumentoRepository->getDocumentos(['id_lugar'=>$id_lugar,'_ordre'=>'id_doc']);
        $d = 0;
        $ident_num = '';
        foreach ($cDocumentos as $oDocumento) {
            $id_doc = $oDocumento->getId_doc();
            $identificador = $oDocumento->getIdentificador();
            $identificador = empty($identificador)? '' : $identificador ;
            // miro donde està.
            $cWhereis = $WhereisRepository->getWhereare(['id_doc'=>$id_doc,'id_item_egm'=>$id_item_egm]);
            if (count($cWhereis) === 0) { // no está en su sitio.
                $id_tipo_doc = $oDocumento->getId_tipo_doc();
                $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
                if ($oTipoDoc === null) {
                    continue;
                }
                // si hay varios hay que contar.
                if (!empty($aCambios[$id_equipaje][$id_item_egm]['out'][$id_tipo_doc])) {
                    $d++;
                    if ($d > 1 && !empty($identificador)) {
                        $ident_num .= ',';
                    }
                    $ident_num .= $identificador;
                    $ident_txt = empty($ident_num)? "" : " ($ident_num)" ;
                    $aCambios[$id_equipaje][$id_item_egm]['out'][$id_tipo_doc] = $d.' '._("ejemplares de").' '.$oTipoDoc->getSigla()." ".$oTipoDoc->getNom_doc().$ident_txt;
                } else {
                    $d = 1;
                    $ident_num = empty($identificador)? '' : "$identificador" ;
                    $ident_txt = empty($identificador)? '' : "($ident_num)" ;
                    $aCambios[$id_equipaje][$id_item_egm]['out'][$id_tipo_doc] = $oTipoDoc->getSigla()." ".$oTipoDoc->getNom_doc().$ident_txt;
                }
            }
        }
        // Poner
        $cWhereis = $WhereisRepository->getWhereare(['id_item_egm'=>$id_item_egm]);
        $ident_lugar = '';
        foreach ($cWhereis as $oWhereis) {
            $id_doc = $oWhereis->getId_doc();
            $oDocumento = $DocumentoRepository->findById((int) $id_doc);
            if ($oDocumento === null) {
                continue;
            }
            $identificador = $oDocumento->getIdentificador();
            $identificador = empty($identificador)? '' : $identificador ;
            // dónde debería estar
            $id_tipo_doc = $oDocumento->getId_tipo_doc();
            $id_lugar_doc = $oDocumento->getId_lugar();
            if ($id_lugar_doc !== null && $id_lugar !== $id_lugar_doc) { // Debo cogerlo de otro sitio.
                $oLugar = $LugarRepository->findById($id_lugar_doc);
                if ($oLugar === null) {
                    continue;
                }
                $nom_lugar = $oLugar->getNom_lugar();
                $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
                if ($oTipoDoc === null) {
                    continue;
                }
                // si hay varios hay que contar.
                $id_compuesto = $id_tipo_doc.'#'.$id_lugar;
                if (!empty($aCambios[$id_equipaje][$id_item_egm]['in'][$id_compuesto])) {
                    $d++;
                    if ($d > 1 && !empty($identificador)) {
                        $ident_num .= ',';
                    }
                    $ident_num .= $identificador;
                    $ident_txt = empty($ident_num)? " [$ident_lugar]" : " [$ident_lugar: ($ident_num)]" ;
                    $aCambios[$id_equipaje][$id_item_egm]['in'][$id_compuesto] = $d.' '._("ejemplares de").' '.$oTipoDoc->getSigla()." ".$oTipoDoc->getNom_doc().$ident_txt;
                } else {
                    $d = 1;
                    $ident_num = empty($identificador)? '' : "$identificador" ;
                    $ident_lugar = _("de")." $nom_lugar";
                    $ident_txt = empty($identificador)? $ident_lugar : "$ident_lugar: ($ident_num)" ;
                    $aCambios[$id_equipaje][$id_item_egm]['in'][$id_compuesto] = $oTipoDoc->getSigla()." ".$oTipoDoc->getNom_doc().$ident_txt;
                }
            }
        }
    }
}

$data = [
    'aCambios' => $aCambios,
    'aLugaresPorEgm'  => $aLugaresPorEgm,
    'aNomEquipajes' => $aNomEquipajes,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);