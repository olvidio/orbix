<?php


// INICIO Cabecera global de URL de controlador *********************************
use Illuminate\Http\JsonResponse;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use ubis\model\entity\GestorCentroEllos;
use ubis\model\entity\GestorCentroEllas;
use ubis\model\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ctr = (integer)filter_input(INPUT_POST, 'id_ctr');
$Qctr_otras_zonas = (integer)filter_input(INPUT_POST, 'ctr_otras_zonas');

//echo $Qid_zona.'#'.$Qid_ctr.'s'.$Qctr_otras_zonas;

$desplegable_ctr='<SELECT ID="id_ctr">';

$oCentro = Ubi::newUbi($Qid_ctr);
$nombre_ctr = $oCentro->getNombre_ubi();
$key = $Qid_ctr . '#' . $nombre_ctr;
$desplegable_ctr.='<OPTION VALUE="'.$key.'">'.$nombre_ctr.'</OPTION>';

$aCentros = [];
$aWhere = [];
$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrossv = new GestorCentroEllos();
$cCentrossv = $GesCentrossv->getCentros($aWhere);
$GesCentrosSf = new GestorCentroEllas();
$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
$cCentros = array_merge($cCentrossv, $cCentrosSf);
        
foreach ($cCentros as $oCentro) {
    $id_ctr = $oCentro->getId_ubi();
    $nombre_ctr = $oCentro->getNombre_ubi();
    $desplegable_ctr.='<OPTION VALUE="'.$id_ctr.'">'.$nombre_ctr.'</OPTION>';    
}    
   

$desplegable_ctr.='</SELECT>';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_ctr;

(new JsonResponse($jsondata))->send();
