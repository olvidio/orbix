<?php
/*
 *  Está como include en 'home_persona.phtml' y 'home_ubis.phtml'
 */

use core\ConfigGlobal;
use core\ViewPhtml;
use dossiers\model\entity\Dossier;
use dossiers\model\entity\GestorTipoDossier;
use web\Hash;

$aWhere = ['tabla_from' => $pau,
        '_ordre' => 'descripcion',
];
$GesTipoDossier = new GestorTipoDossier();
$cTipoDossier = $GesTipoDossier->getTiposDossiers($aWhere);
$i = 0;
$a_filas = [];
$oPermDossier = new dossiers\model\PermDossier();
foreach ($cTipoDossier as $oTipoDossier) {
    $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
    $tabla_from = $oTipoDossier->getTabla_from();
    $tabla_to = $oTipoDossier->getTabla_to();
    $app = $oTipoDossier->getApp();
    $descripcion = $oTipoDossier->getDescripcion();
    $permiso_lectura = $oTipoDossier->getPermiso_lectura();
    $permiso_escritura = $oTipoDossier->getPermiso_escritura();
    $depende_modificar = $oTipoDossier->getDepende_modificar();
    $id_dossier = $id_tipo_dossier;
    // Miro si la app está instalada
    if (!ConfigGlobal::is_app_installed($app)) continue;
    $aWhere = array('tabla' => $tabla_from, 'id_pau' => $id_pau, 'id_tipo_dossier' => $id_tipo_dossier, '_ordre' => 'id_tipo_dossier');
    if (ConfigGlobal::mi_ambito() === 'rstgr') { // las regiones no saben si está abierto o cerrado, lo ven todo:
        $status_dossier = 'f';
    } else {
        $oDossier = new Dossier($aWhere);
        $status_dossier = $oDossier->getStatus_dossier();
    }
    switch ($status_dossier) {
        case "t":
            $a_filas[$i]['imagen'] = ConfigGlobal::getWeb_icons() . '/folder.open.gif';
            break;
        case "f":
        default:
            $a_filas[$i]['imagen'] = ConfigGlobal::getWeb_icons() . '/folder.gif';
            break;
    }
    $a_filas[$i]['clase'] = $i % 2 ? 'imp' : 'par';
    $a_filas[$i]['descripcion'] = $descripcion;
    $perm_a = $oPermDossier->permiso($permiso_lectura, $permiso_escritura, $depende_modificar, $pau, $id_pau);

    $a_filas[$i]['href_ver'] = Hash::link(ConfigGlobal::getWeb() . '/apps/dossiers/controller/dossiers_ver.php?' . http_build_query(array('pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau, 'id_dossier' => $id_dossier, 'permiso' => $perm_a, 'depende' => $depende_modificar)));
    $a_filas[$i]['href_abrir'] = Hash::link(ConfigGlobal::getWeb() . '/apps/dossiers/controller/dossier_abrir.php?' . http_build_query(array('pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau, 'id_dossier' => $id_dossier, 'tabla_to' => $tabla_to, 'permiso' => $perm_a)));
    $a_filas[$i]['perm_a'] = $perm_a;
    $i++;
}

$oView = new ViewPhtml('dossiers\controller');
$oView->renderizar('lista_dossiers.phtml', array('a_filas' => $a_filas));
