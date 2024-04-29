<?php

use core\ConfigGlobal;use usuarios\model\entity as usuarios;

/**
 * Página que presentará los formularios de los distintos plannings
 * Según sea el submenú seleccionado seleccionará el formulario
 * correspondiente
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once('apps/core/global_header.inc');
// Archivos requeridos por esta url **********************************************


// Crea los objetos de uso global **********************************************
require_once('apps/core/global_object.inc');
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv = core\ConfigGlobal::mi_sfsv();

$mes = date('m');
$fin_m = $_SESSION['oConfig']->getMesFinStgr();
if ($mes > $fin_m) {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/%s hasta 31/5)"), $fin_m + 1);
} else {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/6 hasta 30/%s)"), $fin_m + 1);
}
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = (string)filter_input(INPUT_POST, 'na');

// para formato fecha del javascript:
$locale_us = ConfigGlobal::is_locale_us();

//personas
$oHash = new web\Hash();
$oHash->setCamposForm('nombre!apellido1!apellido2!centro!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('modelo');
$a_camposHidden = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
    'na' => $Qna
);
$oHash->setArraycamposHidden($a_camposHidden);
// centros
$oHash1 = new web\Hash();
$oHash1->setCamposForm('sacd!ctr!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!periodo!year');
$oHash1->setcamposNo('todos_n!todos_agd!todos_s!modelo');
$a_camposHidden1 = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
);
$oHash1->setArraycamposHidden($a_camposHidden1);
//casas
$oHash2 = new web\Hash();
$oHash2->setCamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!periodo!year');
$oHash2->setcamposNo('id_cdc!sin_activ!modelo');
$a_camposHidden2 = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau
);
$oHash2->setArraycamposHidden($a_camposHidden2);

$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo del planning actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel(date('Y'));

switch ($Qtipo) {
    case 'planning':
    case 'p_de_paso':
        $personas_txt = '';
        switch ($Qobj_pau) {
            case 'PersonaN':
                $personas_txt = _("numerarios");
                break;
            case 'PersonaNax':
                $personas_txt = _("nax");
                break;
            case 'PersonaAgd':
                $personas_txt = _("agregados");
                break;
            case 'PersonaS':
                $personas_txt = _("supernumerarios");
                break;
            case 'PersonaSSSC':
                $personas_txt = _("de la sss+");
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }
        //cuando queramos visualizar el calendario de actividades de
        //1 persona de dlb o de paso
        $a_campos = ['oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'oFormP' => $oFormP,
            'personas_txt' => $personas_txt,
            'locale_us' => $locale_us,
        ];

        $oView = new core\View('asistentes/controller');
        $oView->renderizar('planning_persona_que.phtml', $a_campos);
        break;
    case 'planning_ctr':
        //cuando queramos visualizar el calendario de actividades de
        //todas las personas de 1 ctr
        $a_campos = ['oPosicion' => $oPosicion,
            'oHash1' => $oHash1,
            'oFormP' => $oFormP,
            'locale_us' => $locale_us,
        ];

        $oView = new core\View('asistentes/controller');
        $oView->renderizar('planning_ctr_que.phtml', $a_campos);
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}