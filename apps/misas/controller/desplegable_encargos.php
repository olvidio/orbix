<?php


// INICIO Cabecera global de URL de controlador *********************************
use Illuminate\Http\JsonResponse;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_encargo = (integer)filter_input(INPUT_POST, 'id_enc');

$grupo = '8...';
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '^' . $grupo;
$aOperador['id_tipo_enc'] = '~';
$oGesEncargoTipo = new GestorEncargoTipo();
$cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);

$a_tipo_enc = [];
$posibles_encargo_tipo = [];
foreach ($cEncargoTipos as $oEncargoTipo) {
    if ($oEncargoTipo->getId_tipo_enc()>=8100) {
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
        $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
    }
}

$desplegable_encargos='<SELECT ID="id_enc">';
$oEncargo_seleccionado = new Encargo($Qid_encargo);
$oEncargo_seleccionado->DBCarregar();
$desc_enc = $oEncargo_seleccionado->getDesc_enc();
$desplegable_encargos.='<OPTION VALUE="'.$Qid_encargo.'">'.$desc_enc.'</OPTION>';

$aWhere = [];
$aOperador = [];
$aEncargos = [];
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);
foreach ($cEncargos as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $desplegable_encargos.='<OPTION VALUE="'.$id_enc.'">'.$desc_enc.'</OPTION>';
}

$desplegable_encargos.='</SELECT>';

$jsondata['mensaje']='mensaje de desplegable';
$jsondata['desplegable']=$desplegable_encargos;

(new JsonResponse($jsondata))->send();
