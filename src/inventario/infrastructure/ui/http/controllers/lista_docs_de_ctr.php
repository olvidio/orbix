<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi');
$Qid_lugar = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_lugar');
$error_txt = '';

$colTipoDoc = [];

/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var ColeccionRepositoryInterface $ColeccionRepository */
$ColeccionRepository = DependencyResolver::get(ColeccionRepositoryInterface::class);
/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);

$aWhere = [];
if (!empty($Qid_ubi)) {
    $aWhere['id_ubi'] = $Qid_ubi;
}
if (!empty($Qid_lugar)) {
    $aWhere['id_lugar'] = $Qid_lugar;
}

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos($aWhere);

$d = 0;
$a_valores = [];
$a_llave = [];
$a_tipo = [];
$a_lugar = [];
$a_nom_coleccion = [];
foreach ($cDocumentos as $oDocumento) {
    $d++;
    $id_doc = $oDocumento->getId_doc();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $observ = $oDocumento->getObservVo()?->value();
    $num_reg = $oDocumento->getNum_reg();
    $num_ejemplares = $oDocumento->getNum_ejemplares();
    // guardo en una colección los tipos de doc para consultas posteriores (de otros lugares).
    if (array_key_exists($id_tipo_doc, $colTipoDoc)) {
        $aTipoDoc = $colTipoDoc[$id_tipo_doc];
        $oTipoDoc = $aTipoDoc['object_tipo'];
        $nom_coleccion = $aTipoDoc['nom_coleccion'];
    } else {
        $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
        if ($oTipoDoc === null) {
            continue;
        }
        $id_coleccion = $oTipoDoc->getId_coleccion();
        if (!empty($id_coleccion)) {
            $oColeccion = $ColeccionRepository->findById((int) $id_coleccion);
            if ($oColeccion === null) {
                continue;
            }
            $nom_coleccion = $oColeccion->getNom_coleccion();

            $colTipoDoc[$id_tipo_doc] = ['object_tipo' => $oTipoDoc, 'nom_coleccion' => $nom_coleccion];
        } else {
            $nom_coleccion = '';
        }
    }

    $lugar = '';
    if (!empty($id_lugar)) {
        $oLugar = $LugarRepository->findById((int) $id_lugar);
        if ($oLugar === null) {
            continue;
        }
        $lugar = $oLugar->getNom_lugar();
    }

    $nom_compost = '';
    $nom_compost .= ($oTipoDoc->isBajo_llave()) ? '* ' : '';
    $nom_compost .= $oTipoDoc->getSigla();
    $nom_doc = $oTipoDoc->getNom_doc();
    $nom_compost .= empty($nom_doc) ? '' : ' ' . $nom_doc;
    $nom_compost .= !empty($num_reg) ? ", $num_reg" : '';
    $nom_compost .= (!empty($num_ejemplares) and $num_ejemplares > 1) ? " ($num_ejemplares ej.)" : '';
    if (!empty($num_ini)) {
        $nom_compost .= " $num_ini" . "-";
        $nom_compost .= !empty($num_fin) ? " $num_fin" : _("Actual");
    }
    $a_valores[$d]['sel'] = $id_doc;
    $a_valores[$d][1] = array('clase' => 'doc', 'valor' => $nom_compost);
    $a_valores[$d][2] = $observ;
    //para poder ordenar
    $a_llave[$d] = $oTipoDoc->isBajo_llave();
    $a_tipo[$d] = $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc();
    $a_lugar[$d] = $lugar;
    $a_nom_coleccion[$d] = $nom_coleccion;
}
if (!empty($a_valores)) {
    array_multisort($a_lugar, SORT_ASC,
        $a_llave, SORT_NUMERIC, SORT_DESC,
        $a_nom_coleccion, SORT_ASC,
        $a_tipo, SORT_ASC, SORT_NATURAL,
        $a_valores);
}

$data = [
    'a_valores' => $a_valores,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
