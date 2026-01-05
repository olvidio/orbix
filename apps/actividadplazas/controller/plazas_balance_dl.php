<?php

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividades\domain\value_objects\StatusId;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\TablaEditable;
use web\TiposActividades;

/**
 * Muestra un cuadro (grid editable) con las actividades de la propia dl y
 * de la dl con la que se compara, mostrando las plazas en cada caso.
 *
 * @param string $dl nombre de la dl a comparar
 * @param integer $id_tipo_activ selección por el tipo de actividad
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qdl = (string)filter_input(INPUT_POST, 'dl');
// Sólo las del tipo...
$Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');

$oTipoActiv = new TiposActividades($Qid_tipo_activ);
$sactividad = $oTipoActiv->getActividadText();

$dlA = ConfigGlobal::mi_delef();
if (empty($Qdl)) {
    die();
} else {
    $dlB = $Qdl;
}
// no puedo compararme conmigo mismo:
if ($dlA === $dlB) {
    die();
}

//periodo
switch ($sactividad) {
    case 'ca':
    case 'cv':
        $any = $_SESSION['oConfig']->any_final_curs('est');
        $inicurs = core\curso_est("inicio", $any, "est")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "est")->format('Y-m-d');
        break;
    case 'crt':
        $any = $_SESSION['oConfig']->any_final_curs('crt');
        $inicurs = core\curso_est("inicio", $any, "crt")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "crt")->format('Y-m-d');
        break;
}

$status = StatusId::ACTUAL; //actual

// Seleccionar los id_dl del mismo grupo de estudios
$esquema = ConfigGlobal::mi_region_dl();
$a_reg = explode('-', $esquema);
$mi_dl = substr($a_reg[1], 0, -1); // quito la v o la f.
$aWhere = array('region' => $a_reg[0], 'dl' => $mi_dl);
$DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
$cDelegaciones = $DelegacionRepository->getDelegaciones($aWhere);
$oMiDelegacion = $cDelegaciones[0];
$grupo_estudios = $oMiDelegacion->getGrupoEstudiosVo()->value();

$cDelegaciones = $DelegacionRepository->getDelegaciones(['grupo_estudios' => $grupo_estudios, '_ordre' => 'region,dl']);
$ActividadPlazasRepository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
$ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);

// Seleccionar actividades exportadas de los id_dl
function PlazasAB_por_actividad($dlA, $dlB, $clase)
{
    global $mi_dl, $Qid_tipo_activ, $status, $inicurs, $fincurs;
    global $DelegacionRepository;
    global $ActividadRepository;
    global $ActividadPlazasRepository;

    $service = $GLOBALS['container']->get(AsistenteActividadService::class);

    $cDelegaciones = $DelegacionRepository->getDelegaciones(array('dl' => $dlA));
    $oDelegacionA = $cDelegaciones[0];
    $id_dlA = $oDelegacionA->getIdDlVo()?->value() ?? 0;
    $cDelegaciones = $DelegacionRepository->getDelegaciones(array('dl' => $dlB));
    $oDelegacionB = $cDelegaciones[0];
    $id_dlB = $oDelegacionB->getIdDlVo()?->value() ?? 0;

    $aWhereA = array('dl_org' => $dlA,
        'id_tipo_activ' => '^' . $Qid_tipo_activ,
        'status' => $status,
        'f_ini' => "'$inicurs','$fincurs'",
        '_ordre' => 'f_ini');
    $aOperador = array('id_tipo_activ' => '~', 'f_ini' => 'BETWEEN');
    $cActividadesA = $ActividadRepository->getActividades($aWhereA, $aOperador);
    $i = 0;
    $a_valores = [];
    $sumaConcedidasA = 0;
    $sumaConcedidasB = 0;
    $dlA_c = $dlA . '-c';
    $dlA_l = $dlA . '-l';
    $dlB_c = $dlB . '-c';
    $dlB_l = $dlB . '-l';
    foreach ($cActividadesA as $oActividad) {
        $i++;
        //$id_tipo_activ = $oActividad->getId_tipo_activ();
        $id_activ = $oActividad->getId_activ();
        $nom = $oActividad->getNom_activ();
        $dl_org = $oActividad->getDl_org();

        $a_valores[$i]['id'] = $id_activ;
        $a_valores[$i]['actividad'] = $nom;
        $a_valores[$i]['dlorg'] = $dl_org;

        $libresA = 0;
        $concedidasA = 0;
        $cActividadPlazas = $ActividadPlazasRepository->getActividadesPlazas(array('id_dl' => $id_dlA, 'id_activ' => $id_activ));
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $dl_tabla = $oActividadPlazas->getDl_tabla();
            if ($dl_org === $dl_tabla) {
                $concedidasA = $oActividadPlazas->getPlazas();
            }
        }
        // ocupadas A
        $ocupadasA = $service->getPlazasOcupadasPorDl($id_activ, $dlA);
        if ($ocupadasA < 0) { // No se sabe
            $libresA = '-';
        } else {
            $libresA = $concedidasA - $ocupadasA;
        }
        $sumaConcedidasA += $concedidasA;


        $libresB = 0;
        $concedidasB = 0;
        $txtB = '';
        $cActividadPlazas = $ActividadPlazasRepository->getActividadesPlazas(array('id_dl' => $id_dlB, 'id_activ' => $id_activ));
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $dl_tabla = $oActividadPlazas->getDl_tabla();
            if ($dl_org === $dl_tabla) {
                $concedidasB = $oActividadPlazas->getPlazas();
            }
        }
        // ocupadas B
        $ocupadasB = $service->getPlazasOcupadasPorDl($id_activ, $dlB);
        if ($ocupadasB < 0) { // No se sabe
            $libresB = '-';
        } else {
            $libresB = $concedidasB - $ocupadasB;
        }
        $sumaConcedidasB += $concedidasB;

        $txtB = (empty($concedidasB) && empty($libresB)) ? '' : "$concedidasB ($libresB libres)";

        if ($dlA === $mi_dl) {
            $a_valores[$i][$dlA_c] = array('editable' => 'true', 'valor' => $concedidasA);
            $a_valores[$i][$dlA_l] = array('editable' => 'false', 'valor' => $libresA);
            //$a_valores[$i][4] = array('editable' => 'true', 'valor' => $txtB);
            $a_valores[$i][$dlB_c] = array('editable' => 'true', 'valor' => $concedidasB);
            $a_valores[$i][$dlB_l] = array('editable' => 'false', 'valor' => $libresB);
        } else {
            //$a_valores[$i][3] = array('editable' => 'false', 'valor' => $txtB);
            $a_valores[$i][$dlB_c] = array('editable' => 'false', 'valor' => $concedidasB);
            $a_valores[$i][$dlB_l] = array('editable' => 'false', 'valor' => $libresB);
            $a_valores[$i][$dlA_c] = array('editable' => 'true', 'valor' => $concedidasA);
            $a_valores[$i][$dlA_l] = array('editable' => 'false', 'valor' => $libresA);
        }
        $a_valores[$i]['clase'] = $clase;
    }
    return array('plazasA' => $sumaConcedidasA, 'plazasB' => $sumaConcedidasB, 'a_valores' => $a_valores);
}

$a_plazasA = PlazasAB_por_actividad($dlA, $dlB, 'tono1');
$concedidasA2B = $a_plazasA['plazasB'];
$a_valoresA = $a_plazasA['a_valores'];

$a_plazasB = PlazasAB_por_actividad($dlB, $dlA, 'tono2');
$concedidasB2A = $a_plazasB['plazasB'];
$a_valoresB = $a_plazasB['a_valores'];

$a_valores = array_merge($a_valoresA, $a_valoresB);

$a_cabeceras = array(
    array('field' => 'id', 'name' => _("id_activ"), 'visible' => 'no'),
    array('field' => 'actividad', 'name' => ucfirst(_("actividad")), 'width' => 100, 'formatter' => 'clickFormatter'),
    array('field' => 'dlorg', 'name' => _("dl org"), 'width' => 10),
);

$a_cabeceras[] = array('name' => $dlA . '-c', 'title' => _("concedidas"), 'field' => $dlA . "-c", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');
$a_cabeceras[] = array('name' => $dlA . '-l', 'title' => _("libres"), 'field' => $dlA . "-l", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');
$a_cabeceras[] = array('name' => $dlB . '-c', 'title' => _("concedidas"), 'field' => $dlB . "-c", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');
$a_cabeceras[] = array('name' => $dlB . '-l', 'title' => _("libres"), 'field' => $dlB . "-l", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');


$a_botones = [];

$oTabla = new TablaEditable();
$oTabla->setId_tabla('plazas_balance');
$UpdateUrl = ConfigGlobal::getWeb() . '/apps/actividadplazas/controller/gestion_plazas_ajax.php';
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'dlA' => $dlA,
    'dlB' => $dlB,
    'concedidasA2B' => $concedidasA2B,
    'concedidasB2A' => $concedidasB2A,
    'oTabla' => $oTabla,
];

$oView = new ViewPhtml('actividadplazas\controller');
$oView->renderizar('plazas_balance_dl.phtml', $a_campos);