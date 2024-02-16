<?php

// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\PersonaSacd;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\Desplegable;
use web\Hash;
use web\Lista;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

// ctr de la zona
$aWhere = [];
$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrosDl = new GestorCentroDl();
$cCentrosDl = $GesCentrosDl->getCentros($aWhere);
$GesCentrosSf = new GestorCentroEllas();
$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
$cCentros = array_merge($cCentrosDl, $cCentrosSf);

/*
$a_cabeceras = ['centros de la zona'];
$a_valores = [];
$i = 0;
$oHash = new Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/misas/controller/lista_ctr_zona.php');
foreach ($cCentros as $oCentro) {
    $i++;
    $id_ubi = $oCentro->getId_ubi();
    $oHash->setArrayCamposHidden(['id_zona' => $Qid_zona, 'id_ubi' => $id_ubi]);
    $param = $oHash->getParamAjax();

    //$data = json_encode((array)$param);

    $ctr = $oCentro->getNombre_ubi();
    $a_valores[$i][0] = "<span class='link' onclick='fnjs_añadir_centro(\"$param\")' >$ctr</span>";
}

$oTabla = new Lista();
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$a_campos = ['oPosicion' => $oPosicion,
		'oTabla' => $oTabla,
];
*/

$aCentros = [];
foreach ($cCentros as $oCentro) {
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aCentros[$id_ubi] = $nombre_ubi;
}


$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($aCentros);

$aTareas=['1'=>'misa', '2'=>'bendición', '3'=>'retiro'];
$oDesplTareas= new Desplegable();
$oDesplTareas->setNombre('id_tarea');
$oDesplTareas->setOpciones($aTareas);

$oHash = new Hash();
$oHash->setArrayCamposHidden(['que' => 'anadir']);
$oHash->setCamposForm('id_ubi!id_tarea');
$h_anadir = $oHash->getParamAjax();


$a_campos = [
    'oDesplCentros' => $oDesplCentros,
    'oDesplTareas' => $oDesplTareas,
    'h' => $h_anadir,
    ];



$oView = new core\ViewTwig('misas/controller');
echo $oView->render('lista_ctr.html.twig', $a_campos);