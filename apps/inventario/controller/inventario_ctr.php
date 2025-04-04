<?php

// INICIO Cabecera global de URL de controlador *********************************
use inventario\domain\repositories\ColeccionRepository;
use inventario\domain\repositories\DocumentoRepository;
use inventario\domain\repositories\LugarRepository;
use inventario\domain\repositories\TipoDocRepository;
use inventario\domain\repositories\UbiInventarioRepository;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$error_txt = '';

$colTipoDoc = array();

$UbiInventarioRepository = new UbiInventarioRepository();
$DocumentoRepository = new DocumentoRepository();
$TipoDocRepository = new TipoDocRepository();
$ColeccionRepository = new ColeccionRepository();
$LugarRepository = new LugarRepository();

$a_ubi_valores = [];
$a_ubi_llave = [];
$a_ubi_tipo = [];
$a_ubi_lugar = [];
$a_ubi_nom_coleccion = [];
foreach ($a_sel as $id_ubi) {
    $oUbiDoc = $UbiInventarioRepository->findById($id_ubi);
    $nombre_ubi = $oUbiDoc->getNom_ubi();

    $cDocumentos = $DocumentoRepository->getDocumentos(['id_ubi' => $id_ubi, 'eliminado' => 'f']);

    $d = 0;
    $a_valores = [];
    $a_llave = [];
    $a_tipo = [];
    $a_lugar = [];
    $a_nom_coleccion = [];
    foreach ($cDocumentos as $oDocumento) {
        $d++;
        $id_tipo_doc = $oDocumento->getId_tipo_doc();
        $observ = $oDocumento->getObservCtr();
        $num_ejemplares = $oDocumento->getNum_ejemplares();
        // guardo en una colección los tipos de doc para consultas posteriores (de otros lugares).
        if (array_key_exists($id_tipo_doc, $colTipoDoc)) {
            $aTipoDoc = $colTipoDoc[$id_tipo_doc];
            $oTipoDoc = $aTipoDoc['object_tipo'];
            $nom_coleccion = $aTipoDoc['nom_coleccion'];
        } else {
            $oTipoDoc = $TipoDocRepository->findById($id_tipo_doc);
            $id_coleccion = $oTipoDoc->getId_coleccion();
            if (!empty($id_coleccion)) {
                $oColeccion = $ColeccionRepository->findById($id_coleccion);
                $nom_coleccion = $oColeccion->getNom_coleccion();

                $colTipoDoc[$id_tipo_doc] = ['object_tipo' => $oTipoDoc, 'nom_coleccion' => $nom_coleccion];
            } else {
                $nom_coleccion = '';
            }
        }

        $lugar = '';
        if (!empty($id_lugar)) {
            $oLugar = $LugarRepository->findById($id_lugar);
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
        $a_valores[$d][1] = array('clase' => 'check', 'valor' => "<input type=checkbox>");
        $a_valores[$d][2] = array('clase' => 'doc', 'valor' => $nom_compost);
        $a_valores[$d][3] = $observ;
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
            $a_tipo, SORT_ASC,
            $a_valores);

        $a_ubi_valores[$nombre_ubi] = $a_valores;
        $a_ubi_llave[$nombre_ubi] = $a_llave;
        $a_ubi_tipo[$nombre_ubi] = $a_tipo;
        $a_ubi_lugar[$nombre_ubi] = $a_lugar;
        $a_ubi_nom_coleccion[$nombre_ubi] = $a_nom_coleccion;
    }

}

$data = [
 'a_valores' => $a_ubi_valores,
 'a_llave' => $a_ubi_llave,
 'a_tipo' => $a_ubi_tipo,
 'a_lugar' => $a_ubi_lugar,
 'a_nom_coleccion' => $a_ubi_nom_coleccion,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);