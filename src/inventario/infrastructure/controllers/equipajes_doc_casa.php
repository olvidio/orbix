<?php


// INICIO Cabecera global de URL de controlador *********************************
use src\inventario\application\repositories\ColeccionRepository;
use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\application\repositories\EquipajeRepository;
use src\inventario\application\repositories\LugarRepository;
use src\inventario\application\repositories\TipoDocRepository;
use src\inventario\application\repositories\UbiInventarioRepository;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$error_txt = '';

$EquipajeRepository = new EquipajeRepository();
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
$id_ubi_activ = $oEquipaje->getId_ubi_activ();
$nombre_lugar = $oEquipaje->getLugar();

$DocumentoRepository = new DocumentoRepository();
$TipoDocRepository = new TipoDocRepository();
$LugarRepository = new LugarRepository();
$UbiInventarioRepository = new UbiInventarioRepository();
$cUbsiInventario = $UbiInventarioRepository->getUbisInventario(['id_ubi_activ' => $id_ubi_activ]);
$a_valores = [];
$id_ubi = null;
if (is_array($cUbsiInventario) && !empty($cUbsiInventario)) {

    //para ordenar por colecciones:
    $aColeccion = [];
    $ColeccionRepository = new ColeccionRepository();
    $cColecciones = $ColeccionRepository->getColecciones(array('_ordre' => 'nom_coleccion'));
    foreach ($cColecciones as $oColeccion) {
        $aColeccion[$oColeccion->getId_coleccion()] = $oColeccion->isAgrupar();
    }

    $nombre_ubi = $cUbsiInventario[0]->getNom_ubi();
    $id_ubi = $cUbsiInventario[0]->getId_ubi();

    $cDocumentos = $DocumentoRepository->getDocumentos(['id_ubi' => $id_ubi, 'eliminado' => 'f']);

    $d = 0;
    $a_tipo = [];
    $a_num = [];
    $a_lugar = [];
    $a_orden_coleccion = [];
    foreach ($cDocumentos as $oDocumento) {
        $d++;
        $identificador = $oDocumento->getIdentificador();
        $num_ejemplares = $oDocumento->getNum_ejemplares();
        $observ = $oDocumento->getObserv();
        $id_tipo_doc = $oDocumento->getId_tipo_doc();
        $id_lugar_doc = $oDocumento->getId_lugar();
        $oTipoDoc = $TipoDocRepository->findById($id_tipo_doc);
        if ($oTipoDoc === null) {
            throw new \Exception("Documento no encontrado con ID: " . $id_tipo_doc);
        }
        $lugar = '';
        if (!empty($id_lugar_doc)) {
            $oLugar = $LugarRepository->findById($id_lugar_doc);
            if ($oLugar === null) {
                throw new \Exception("Lugar no encontrado con ID: " . $id_lugar_doc);
            }
            $lugar = $oLugar->getNom_lugar();
        }

        if (!empty($num_ejemplares) && $num_ejemplares > 1) {
            $a_valores[$d][1] = $num_ejemplares . ' ' . _("ejemplares de") . ' ';
        } else {
            $a_valores[$d][1] = '';
        }
        $a_valores[$d][1] .= $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc() . " " . $observ;
        $a_valores[$d][2] = $identificador;
        $a_valores[$d][3] = $lugar;
        //para poder ordenar
        $a_tipo[$d] = $a_valores[$d][1];
        $a_num[$d] = $a_valores[$d][2];
        $a_lugar[$d] = $a_valores[$d][3];

        // primero los que tienen identificador y no son cartas, cartas, sin identificador
        $id_coleccion = $oTipoDoc->getId_coleccion();
        if ($id_coleccion !== null) {
            $bcarta = $aColeccion[$id_coleccion];
            if (!empty($identificador) && !$bcarta) {
                $orden[$d] = 1;
            } elseif ($bcarta) {
                $orden[$d] = 2;
            } else {
                $orden[$d] = 3;
            }
        } else { // documentos sin colección
            $orden[$d] = 4;
            $bcarta = '';
        }

        $a_valores[$d][4] = empty($id_coleccion) ? false : $id_coleccion;
        $a_valores[$d][5] = empty($bcarta) ? false : $bcarta;
        // para ordenar
        $a_orden_coleccion[$d] = $a_valores[$d][4];
    }
    //array_multisort($a_lugar, SORT_ASC, $a_tipo, SORT_ASC, $a_valores);
    // ordenar por sigla
    if (!empty($a_valores)) {
        array_multisort($a_lugar, SORT_ASC,
            $orden, SORT_NUMERIC, SORT_DESC,
            $a_orden_coleccion, SORT_NUMERIC, SORT_ASC,
            $a_num, SORT_NUMERIC, SORT_ASC,
            $a_tipo, SORT_ASC, SORT_NATURAL,
            $a_valores);

    }

}


$data = [
    'a_valores' => $a_valores,
    'nombre_ubi' => $nombre_ubi ?? $nombre_lugar,
    'id_ubi' => $id_ubi,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
