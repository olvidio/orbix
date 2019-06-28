<?php 

/**
* Esta página lista las actividades de s y sg.
* Para asignar la previsión de asistentes
*
*@package	delegacion
*@subpackage actividades
*@author	Daniel Serrabou
*@since		15/3/09.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use function core\strtoupper_dlb;
use web\DateTimeLocal;
use web\Desplegable;
use web\PeriodoQue;
use actividades\model\entity\GestorActividadDl;
use casas\model\entity\Ingreso;
use ubis\model\entity\CasaDl;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();


$Qmi_of = (string) \filter_input(INPUT_POST, 'mi_of');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (integer) \filter_input(INPUT_POST, 'year');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');


$aWhere = [];
$aOperador = [];
// periodo.
// valores por defeccto
// desde todo el año
if (empty($Qempiezamin)) {
    $QempiezaminIso = date('Y-m-d',mktime(0, 0, 0, 1, 1, date('Y')));
} else {
    $oEmpiezamin = DateTimeLocal::createFromLocal($Qempiezamin);
    $QempiezaminIso = $oEmpiezamin->getIso();
}
//hasta
if (empty($Qempiezamax)) {
    $QempiezamaxIso = date('Y-m-d',mktime(0, 0, 0, 12, 31, date('Y')));
} else {
    $oEmpiezamax = DateTimeLocal::createFromLocal($Qempiezamax);
    $QempiezamaxIso = $oEmpiezamax->getIso();
}
if (empty($Qperiodo) || $Qperiodo == 'otro') {
    $Qinicio = empty($Qinicio)? $QempiezaminIso : $Qinicio;
    $Qfin = empty($Qfin)? $QempiezamaxIso : $Qfin;
} else {
    $oPeriodo = new web\Periodo();
    $any=empty($Qyear)? date('Y')+1 : $Qyear;
    $oPeriodo->setAny($any);
    $oPeriodo->setPeriodo($Qperiodo);
    $Qinicio = $oPeriodo->getF_ini_iso();
    $Qfin = $oPeriodo->getF_fin_iso();
}
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
    $aWhere['f_fin'] = "'$Qinicio','$Qfin'";
    $aOperador['f_fin'] = 'BETWEEN';
} else {
    $aWhere['f_ini'] = "'$Qinicio','$Qfin'";
    $aOperador['f_ini'] = 'BETWEEN';
}

$mi_of= empty($Qmi_of)? ConfigGlobal::mi_oficina() : $Qmi_of;
$mi_sfsv = ConfigGlobal::mi_sfsv();

//tipo actividad
$aOperador['id_tipo_activ'] = '~';
switch ($mi_of) {
    case "sm":
        $aWhere['id_tipo_activ'] = '^'.$mi_sfsv.'1';
        break;
    case "nax":
        $aWhere['id_tipo_activ'] = '^'.$mi_sfsv.'2';
        break;
    case "agd":
        $aWhere['id_tipo_activ'] = '^'.$mi_sfsv.'3';
        break;
    case "sg":
        $aWhere['id_tipo_activ'] = '^'.$mi_sfsv.'[45]';
        break;
    case "des":
        $condicion="(a.id_tipo_activ::text ~ '^16' OR a.id_tipo_activ =114031 OR a.id_tipo_activ =112030 OR a.id_tipo_activ::text ~ '^13403[012]' )";
        break;
    case "sr":
        $aWhere['id_tipo_activ'] = '^'.$mi_sfsv.'7';
        break;
    default:
        if ($_SESSION['oConfig']->getGestionCalendario() == 'central') { // central => centralizada, oficinas => por oficinas.
            $aWhere['id_tipo_activ'] = '^'.$mi_sfsv;
        } else {
            exit (_("No tiene actividades asignadas a su oficina"));
        }
}

$aWhere['_ordre'] = 'f_ini';
$GesActividadesDl = new GestorActividadDl();
$cActividades = $GesActividadesDl->getActividades($aWhere,$aOperador);
$i = 0;
$a_valores = [];
foreach($cActividades as $oActividad) {
    $i++;
    $id_activ = $oActividad->getId_activ();
    $nom_activ = $oActividad->getNom_activ();
    $id_ubi = $oActividad->getId_ubi();
    
    $Ingreso = new Ingreso($id_activ);
    $num_asistentes_previstos = $Ingreso->getNum_asistentes_previstos();
    
    $Ubi = new CasaDl($id_ubi);
    $plazas = $Ubi->getPlazas();
    $plazas_min = $Ubi->getPlazas_min();
    
    $a_valores[$i]['clase'] = 'tono2';
    $a_valores[$i]['id'] = $id_activ;
    $a_valores[$i]['actividad'] = ['editable' => 'false', 'valor' => $nom_activ];
    $a_valores[$i]['plazas'] = ['editable' => 'false', 'valor' => $plazas];
    $a_valores[$i]['plazas_min'] = ['editable' => 'false', 'valor' => $plazas_min];
    $a_valores[$i]['previstas'] = ['editable' => 'true', 'valor' => $num_asistentes_previstos];
}

$a_cabeceras=array();
$a_cabeceras[]=array('name'=>_("id_activ"),'field'=>'id','visible'=>'no');
$a_cabeceras[]=array('name'=>_("actividad"),'field'=>'actividad','width'=>180);
$a_cabeceras[]=array('name'=>_("plazas"),'title'=>_("plazas de la casa"),'field'=>'plazas','width'=>20);
$a_cabeceras[]=array('name'=>_("mínimas"),'title'=>_("plazas mínimas"),'field'=>'plazas_min','width'=>20);
$a_cabeceras[] = array('name'=>_("previstas"),'title'=>_("plazas previstas"),'field'=>"previstas",'width'=>15,'editor'=>'Slick.Editors.Integer','formatter'=>'cssFormatter');

if (empty($mi_of)) {
    $titulo=ucfirst(_("listado de actividades"));
} else {
    $titulo=ucfirst(sprintf(_("listado de actividades de %s"),$mi_of));
}


$aOpciones =  array(
					'tot_any'=> _('todo el año'),
					'trimestre_1'=> _('primer trimestre'),
					'trimestre_2'=> _('segundo trimestre'),
					'trimestre_3'=> _('tercer trimestre'),
					'trimestre_4'=> _('cuarto trimestre'),
					'separador'=>'-------',
					'otro'=> _('otro...')
					);
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(strtoupper_dlb(_("período del listado del año próximo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

$boton = "<input type='button' value='"._("buscar")."' onclick='fnjs_buscar()' >";
$oFormP->setBoton($boton);

if ($_SESSION['oConfig']->getGestionCalendario() == 'central') { // central => centralizada, oficinas => por oficinas.
    $aOpciones =  array(
                    'sm' => 'sm',
                    'nax' => 'nax',
                    'agd' => 'agd',
                    'sg' => 'sg',
                    'sr' => 'sr'
                        );
    $DesplOficinas = new Desplegable();
    $DesplOficinas->setNombre('mi_of');
    $DesplOficinas->setOpciones($aOpciones);
    $DesplOficinas->setOpcion_sel($mi_of);
    $DesplOficinas->setBlanco(1);
    $Antes = _('oficina').'</td><td>'.$DesplOficinas->desplegable();
    $oFormP->setAntes($Antes);
}



$oTabla = new web\TablaEditable();
$oTabla->setId_tabla('prevision_asistentes');
$UpdateUrl = core\ConfigGlobal::getWeb().'/apps/casas/controller/prevision_asistentes_ajax.php';
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setCamposForm('empiezamax!empiezamin!iactividad_val!iasistentes_val!mi_of!periodo!year');
$oHash->setCamposNo('!refresh');

$a_campos = ['oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
];

$oView = new core\ViewTwig('casas/controller');
echo $oView->render('prevision_asistentes.html.twig',$a_campos);