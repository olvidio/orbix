<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$sel = input_string($_POST, 'sel');

$a_sel = json_decode($sel, true);
if (!is_array($a_sel)) {
    $a_sel = [];
}

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

$a_ubi_valores = [];
$a_ubi_llave = [];
$a_ubi_tipo = [];
$a_ubi_lugar = [];
$a_ubi_nom_coleccion = [];
foreach ($a_sel as $id_ubi_raw) {
    if (!is_numeric($id_ubi_raw)) {
        continue;
    }
    $id_ubi = (int) $id_ubi_raw;
    $oUbiDoc = $UbiInventarioRepository->findById($id_ubi);
    if ($oUbiDoc === null) {
        continue;
    }
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
        $id_lugar = $oDocumento->getId_lugar();
        $observ = $oDocumento->getObservVo()?->value();
        $observ_ctr = $oDocumento->getObservCtrVo()?->value();
        $num_ejemplares = $oDocumento->getNumEjemplaresVo()?->value();
        $num_reg = $oDocumento->getNumRegVo()?->value();
        $num_ini = $oDocumento->getNumIniVo()?->value();
        $num_fin = $oDocumento->getNumFinVo()?->value();
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
            $nom_compost .= " desde el $num_ini" . "-";
            $nom_compost .= !empty($num_fin) ? " $num_fin" : _("Actual");
        }
        $a_valores[$d][1] = array('clase' => 'check', 'valor' => "<input type=checkbox>");
        $a_valores[$d][2] = array('clase' => 'doc', 'valor' => $nom_compost);
        $a_valores[$d][3] = $observ_ctr;
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
ContestarJson::enviar($error_txt, $data);