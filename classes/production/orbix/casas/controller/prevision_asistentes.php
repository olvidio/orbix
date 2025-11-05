<?php

/**
 * Esta página lista las actividades de s y sg.
 * Para asignar la previsión de asistentes
 *
 * @package    delegacion
 * @subpackage actividades
 * @author    Daniel Serrabou
 * @since        15/3/09.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************

use actividades\model\entity\GestorActividadDl;
use casas\model\entity\Ingreso;
use core\ConfigGlobal;
use core\ViewTwig;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\Periodo;
use web\PeriodoQue;
use ubis\model\entity\CasaDl;
use web\TablaEditable;
use function core\strtoupper_dlb;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();


$Qmi_of = (string)filter_input(INPUT_POST, 'mi_of');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$aWhere = [];
$aOperador = [];

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);


$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
    $aWhere['f_fin'] = "'$inicioIso','$finIso'";
    $aOperador['f_fin'] = 'BETWEEN';
} else {
    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
    $aOperador['f_ini'] = 'BETWEEN';
}

$mi_of = empty($Qmi_of) ? ConfigGlobal::mi_oficina() : $Qmi_of;
$mi_sfsv = ConfigGlobal::mi_sfsv();

//tipo actividad
$aOperador['id_tipo_activ'] = '~';
switch ($mi_of) {
    case "sm":
        $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '1';
        break;
    case "nax":
        $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '2';
        break;
    case "agd":
        $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '3';
        break;
    case "sg":
        $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '[45]';
        break;
    case "des":
        $condicion = "(a.id_tipo_activ::text ~ '^16' OR a.id_tipo_activ::text ~ '^1141' OR a.id_tipo_activ::text ~ '^1125' OR a.id_tipo_activ::text ~ '^1341' )";
        break;
    case "sr":
        $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '7';
        break;
    default:
        if ($_SESSION['oConfig']->getGestionCalendario() == 'central') { // central => centralizada, oficinas => por oficinas.
            $aWhere['id_tipo_activ'] = '^' . $mi_sfsv;
        } else {
            exit (_("No tiene actividades asignadas a su oficina"));
        }
}

$aWhere['_ordre'] = 'f_ini';
$GesActividadesDl = new GestorActividadDl();
$cActividades = $GesActividadesDl->getActividades($aWhere, $aOperador);
$i = 0;
$a_valores = [];
foreach ($cActividades as $oActividad) {
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

$a_cabeceras = [];
$a_cabeceras[] = array('name' => _("id_activ"), 'field' => 'id', 'visible' => 'no');
$a_cabeceras[] = array('name' => _("actividad"), 'field' => 'actividad', 'width' => 180);
$a_cabeceras[] = array('name' => _("plazas"), 'title' => _("plazas de la casa"), 'field' => 'plazas', 'width' => 20);
$a_cabeceras[] = array('name' => _("mínimas"), 'title' => _("plazas mínimas"), 'field' => 'plazas_min', 'width' => 20);
$a_cabeceras[] = array('name' => _("previstas"), 'title' => _("plazas previstas"), 'field' => "previstas", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');

$aOpciones = array(
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '-------',
    'otro' => _('otro...')
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(strtoupper_dlb(_("período del listado del año próximo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMax($Qempiezamax);
$oFormP->setEmpiezaMin($Qempiezamin);

$boton = "<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >";
$oFormP->setBoton($boton);

if ($_SESSION['oConfig']->getGestionCalendario() == 'central') { // central => centralizada, oficinas => por oficinas.
    $aOpciones = array(
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
    $Antes = _('oficina') . '</td><td>' . $DesplOficinas->desplegable();
    $oFormP->setAntes($Antes);
}

$oTabla = new TablaEditable();
$oTabla->setId_tabla('prevision_asistentes');
$UpdateUrl = ConfigGlobal::getWeb() . '/apps/casas/controller/prevision_asistentes_ajax.php';
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!mi_of!periodo!year');
$oHash->setCamposNo('!refresh');

if (empty($mi_of)) {
    $titulo = ucfirst(_("listado de actividades"));
} else {
    $titulo = ucfirst(sprintf(_("listado de actividades de %s"), $mi_of));
}
// Convertir las fechas inicio y fin a formato local:
$oF_qini = new DateTimeLocal($inicioIso);
$QinicioLocal = $oF_qini->getFromLocal();
$oF_qfin = new DateTimeLocal($finIso);
$QfinLocal = $oF_qfin->getFromLocal();
$titulo .= ' ' . sprintf(_("entre %s y %s"), $QinicioLocal, $QfinLocal);

$a_campos = ['oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
];

$oView = new ViewTwig('casas/controller');
$oView->renderizar('prevision_asistentes.html.twig', $a_campos);