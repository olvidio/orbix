<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_equipaje = input_int($_POST, 'id_equipaje');
$error_txt = '';

/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
if ($oEquipaje === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}
$id_ubi_activ = $oEquipaje->getId_ubi_activ();
$nombre_lugar = $oEquipaje->getLugarVo()?->value() ?? '';

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
$cUbsiInventario = $UbiInventarioRepository->getUbisInventario(['id_ubi_activ' => $id_ubi_activ]);
$a_valores = [];
$id_ubi = null;
if (!empty($cUbsiInventario)) {

    //para ordenar por colecciones:
    $aColeccion = [];
    /** @var ColeccionRepositoryInterface $ColeccionRepository */
$ColeccionRepository = DependencyResolver::get(ColeccionRepositoryInterface::class);
    $cColecciones = $ColeccionRepository->getColecciones(array('_ordre' => 'nom_coleccion'));
    foreach ($cColecciones as $oColeccion) {
        $aColeccion[$oColeccion->getId_coleccion()] = $oColeccion->isAgrupar();
    }

    $nombre_ubi = $cUbsiInventario[0]->getNom_ubi();
    $id_ubi = $cUbsiInventario[0]->getId_ubi();

    $cDocumentos = $DocumentoRepository->getDocumentos(['id_ubi' => $id_ubi, 'eliminado' => 'f']);

    $d = 0;
    $orden = [];
    $a_tipo = [];
    $a_num = [];
    $a_lugar = [];
    $a_orden_coleccion = [];
    foreach ($cDocumentos as $oDocumento) {
        $d++;
        $identificador = $oDocumento->getIdentificador();
        $num_ejemplares = $oDocumento->getNum_ejemplares();
        $observ = $oDocumento->getObservVo()?->value();
        $id_tipo_doc = $oDocumento->getId_tipo_doc();
        $id_lugar_doc = $oDocumento->getId_lugar();
        $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
        if ($oTipoDoc === null) {
            continue;
        }
        $lugar = '';
        if (!empty($id_lugar_doc)) {
            $oLugar = $LugarRepository->findById($id_lugar_doc);
            if ($oLugar === null) {
                continue;
            }
            $lugar = $oLugar->getNom_lugar();
        }

        if (!empty($num_ejemplares) && $num_ejemplares > 1) {
            $a_valores[$d]['ejemplares'] = $num_ejemplares;
            $a_valores[$d]['nombre'] = $num_ejemplares . ' ' . _("ejemplares de") . ' ';
        } else {
            $a_valores[$d]['ejemplares'] = '';
            $a_valores[$d]['nombre'] = '';
        }
        $a_valores[$d]['nombre'] .= $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc() . " " . $observ;
        $a_valores[$d]['identificador'] = $identificador;
        $a_valores[$d]['lugar'] = $lugar;
        //para poder ordenar
        $a_tipo[$d] = $a_valores[$d]['nombre'];
        $a_num[$d] = $a_valores[$d]['identificador'];
        $a_lugar[$d] = $a_valores[$d]['lugar'];

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

        $a_valores[$d]['coleccion'] = empty($id_coleccion) ? false : $id_coleccion;
        $a_valores[$d]['carta'] = empty($bcarta) ? false : $bcarta;
        // para ordenar
        $a_orden_coleccion[$d] = $a_valores[$d]['coleccion'];
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
ContestarJson::enviar($error_txt, $data);
