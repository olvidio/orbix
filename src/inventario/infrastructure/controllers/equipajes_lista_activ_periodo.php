<?php


// INICIO Cabecera global de URL de controlador *********************************
use actividades\model\entity\GestorActividad;
use core\ConfigGlobal;
use src\ubis\domain\entity\Ubi;
use web\ContestarJson;
use web\Periodo;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');

$error_txt = '';
$a_valores = [];
$nombre_ubi = '';
if (empty($Qid_cdc)) {
    $error_txt = _("debe seleccionar un lugar");
} else {
    $miSfsv = ConfigGlobal::mi_sfsv();
// casa de las actividades organizadas por la dl en el periodo.
    $any = empty($Qyear) ? (int)date('Y') + 1 : $Qyear;
    $mes = date("m");
    if (empty($Qperiodo) || $Qperiodo === 'otro') {
        $inicio = empty($Qinicio) ? $Qempiezamin : $Qinicio;
        $fin = empty($Qfin) ? $Qempiezamax : $Qfin;
    } else {
        $oPeriodo = new Periodo();
        $oPeriodo->setAny($any);
        $oPeriodo->setPeriodo($Qperiodo);
        $inicio = $oPeriodo->getF_ini()->getIso();
        $fin = $oPeriodo->getF_fin()->getIso();
    }

    $oUbi = Ubi::NewUbi($Qid_cdc);
    $nombre_ubi = empty($oUbi->getNombre_ubi()) ? 'sin determinar' : $oUbi->getNombre_ubi();

    $aWhere['id_ubi'] = $Qid_cdc;
    $aWhere['dl_org'] = ConfigGlobal::mi_dele();
    $aWhere['id_tipo_activ'] = "^$miSfsv";
    $aOperador['id_tipo_activ'] = "~";
    $aWhere['f_ini'] = $fin;
    $aOperador['f_ini'] = '<=';
    $aWhere['f_fin'] = $inicio;
    $aOperador['f_fin'] = '>=';
    $aWhere['status'] = 4;
    $aOperador['status'] = '<';
    $aWhere['_ordre'] = 'f_ini';
    $GesActividades = new GestorActividad();
    $cActividades = $GesActividades->getActividades($aWhere, $aOperador);

    $a = 0;
    foreach ($cActividades as $oActividad) {
        $a++;
        $id_activ = $oActividad->getId_activ();
        $f_ini = $oActividad->getF_ini()->getFromLocal();
        $f_fin = $oActividad->getF_fin()->getFromLocal();
        $nom_activ = $oActividad->getNom_activ();
        $observ = $oActividad->getObserv();

        $a_valores[$a]['sel'] = ['id' => $id_activ, 'select' => ''];
        $a_valores[$a][1] = $f_ini;
        $a_valores[$a][3] = $f_fin;
        $a_valores[$a][8] = $nom_activ;
        $a_valores[$a][13] = $observ;
    }
}

$data = [
    'a_valores' => $a_valores,
    'nombre_ubi' => $nombre_ubi,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
