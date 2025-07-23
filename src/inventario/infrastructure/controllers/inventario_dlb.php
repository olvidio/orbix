<?php

// INICIO Cabecera global de URL de controlador *********************************
use src\inventario\application\repositories\ColeccionRepository;
use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\application\repositories\LugarRepository;
use src\inventario\application\repositories\TipoDocRepository;
use src\inventario\application\repositories\UbiInventarioRepository;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sel = (string)filter_input(INPUT_POST, 'sel');

$a_sel = json_decode($sel, true);

$error_txt = '';
$colTipoDoc = [];

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
foreach ($a_sel as $id_lugar) {
    $oLugar = $LugarRepository->findById($id_lugar);
    $lugar = $oLugar->getNom_lugar();
    $id_ubi = $oLugar->getId_ubi();

    $oUbiDoc = $UbiInventarioRepository->findById($id_ubi);
    $nombre_ubi = $oUbiDoc->getNom_ubi();

    $cDocumentos = $DocumentoRepository->getDocumentos(['id_lugar' => $id_lugar, 'eliminado' => 'f']);

    $d = 0;
    $a_valores = [];
    $a_llave = [];
    $a_tipo = [];
    $a_lugar = [];
    $a_nom_coleccion = [];
    foreach ($cDocumentos as $oDocumento) {
        $d++;
        $id_tipo_doc = $oDocumento->getId_tipo_doc();
        $observ = $oDocumento->getObserv();
        $num_ejemplares = $oDocumento->getNum_ejemplares();
        $identificador = $oDocumento->getIdentificador();
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
        $a_valores[$d][3]=array('clase'=>'doc','valor'=>$identificador);
        $a_valores[$d][4] = $observ;
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

        $a_ubi_valores[$lugar] = $a_valores;
        $a_ubi_llave[$lugar] = $a_llave;
        $a_ubi_tipo[$lugar] = $a_tipo;
        $a_ubi_lugar[$lugar] = $nombre_ubi;
        $a_ubi_nom_coleccion[$lugar] = $a_nom_coleccion;
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