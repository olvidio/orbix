<?php
/**
 * Muestra el cuadro de calendario: Plazas que tiene cada dl del grupo por actividad.
 *
 * @param integer $id_tipo_activ
 * o bien
 * @param string $ssfsv
 * @param string $sasistentes
 * @param string $ssctividad
 */

// INICIO Cabecera global de URL de controlador *********************************
use ubis\model\entity\GestorDelegacion;
use web\Periodo;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

// Id tipo actividad
$extendida = FALSE;
if (empty($Qid_tipo_activ)) {
    // mejor que novenga por menú. Así solo veo las de mi sección.
    //$Qssfsv = (string)  filter_input(INPUT_POST, 'ssfsv');
    $Qssfsv = '';
    if (empty($Qssfsv)) {
        $mi_sfsv = core\ConfigGlobal::mi_sfsv();
        if ($mi_sfsv == 1) $Qssfsv = 'sv';
        if ($mi_sfsv == 2) $Qssfsv = 'sf';
    }
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsactividad2 = (string)filter_input(INPUT_POST, 'sactividad2');

    if (!empty($Qsactividad2)) {
        $extendida = TRUE;
    }
    $oTipoActiv = new web\TiposActividades();
    $oTipoActiv->setSfsvText($Qssfsv);
    $oTipoActiv->setAsistentesText($Qsasistentes);
    if (!empty($Qsactividad2)) {
        $oTipoActiv->setActividad2DigitosText($Qsactividad2);
    } else {
        $oTipoActiv->setActividadText($Qsactividad);
    }
    $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
} else {
    $oTipoActiv = new web\TiposActividades($Qid_tipo_activ);
    $Qsactividad = $oTipoActiv->getActividadText();
}
$id_tipo_activ = '^' . $Qid_tipo_activ;

if (empty($Qyear)) {
    $Qyear = (integer)date('Y');
}
//periodo
if (empty($Qperiodo)) {
    switch ($Qsactividad) {
        case 'ca':
        case 'cv':
            $Qperiodo = 'curso_ca';
            break;
        case 'crt':
        case 'cve':
            $Qperiodo = 'curso_crt';
            break;
    }
}
$oPeriodo = new Periodo();
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$status = \actividades\model\entity\ActividadAll::STATUS_ACTUAL; //actual

// Seleccionar los id_dl del mismo grupo de estudios
$a_reg = core\ConfigGlobal::mi_region();
$mi_dl = core\ConfigGlobal::mi_delef();
$aWhere = array('region' => $a_reg[0], 'dl' => $mi_dl);
$gesDelegacion = new GestorDelegacion();
$cDelegaciones = $gesDelegacion->getDelegaciones($aWhere);
if (empty($cDelegaciones)) {
    $msg = sprintf(_("No se ha definido ninguna dl='%s' en la región '%s"),$mi_dl, $a_reg[0]);
    exit ($msg);
}
$oMiDelegacion = $cDelegaciones[0];
$grupo_estudios = $oMiDelegacion->getGrupo_estudios();

$cDelegaciones = [];
if (empty($grupo_estudios)) {
    $cDelegaciones[] = $oMiDelegacion;
} else {
    $gesDelegacion = new ubis\model\entity\GestorDelegacion();
    $cDelegaciones = $gesDelegacion->getDelegaciones(array('grupo_estudios' => $grupo_estudios, '_ordre' => 'region,dl'));
}
$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
// Seleccionar actividades exportadas de los id_dl

$a_grupo = array();
$cActividades = array();
$gesActividades = new actividades\model\entity\GestorActividad();
$k = 0;
foreach ($cDelegaciones as $oDelegacion) {
    $k++;
    $dl = $oDelegacion->getDl();
    $id_dl = $oDelegacion->getId_dl();
    $a_grupo[$dl] = $id_dl;
    $aWhere = array('dl_org' => $dl,
        'id_tipo_activ' => $id_tipo_activ,
        'status' => $status,
//					'publicado' 		=> 't',
        'f_ini' => "'$inicioIso','$finIso'",
        '_ordre' => 'publicado,f_ini');
    $aOperador = array('id_tipo_activ' => '~', 'f_ini' => 'BETWEEN');
    $cActividades1 = $gesActividades->getActividades($aWhere, $aOperador);
    $cActividades = array_merge($cActividades, $cActividades1);

}

// Dibujar tabla de plazas por actividad
$i = 0;
$a_valores = array();
foreach ($cActividades as $oActividad) {
    $i++;
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $id_activ = $oActividad->getId_activ();
    $nom = $oActividad->getNom_activ();
    $dl_org = $oActividad->getDl_org();
    $plazas_totales = $oActividad->getPlazas();
    if (empty($plazas_totales)) {
        $id_ubi = $oActividad->getId_ubi();
        $oCasa = ubis\model\entity\Ubi::NewUbi($id_ubi);
        // Si la casa es un ctr de otra dl, no sé las plazas
        if (method_exists($oCasa, 'getPlazas')) {
            $plazas_totales = $oCasa->getPlazas();
        } else {
            $plazas_totales = '';
        }
        if (empty($plazas_totales)) {
            $plazas_totales = '?';
        }
    }
    // para estilos
    if ($mi_dl == $dl_org) {
        $a_valores[$i]['clase'] = 'tono2';
    }
    //echo "$nom     $id_tipo_activ       $dl_org".'<br>';
    $a_valores[$i]['id'] = $id_activ;
    $a_valores[$i]['actividad'] = $nom;
    $a_valores[$i]['dlorg'] = $dl_org;
    $a_valores[$i]['tot'] = $plazas_totales;
    if ($mi_dl == $dl_org) {
        $a_valores[$i]['tot'] = array('editable' => 'true', 'valor' => $plazas_totales);
    } else {
        $a_valores[$i]['tot'] = array('editable' => 'false', 'valor' => $plazas_totales);
    }
    foreach ($a_grupo as $dl => $id_dl) {
        $pedidas = '-';
        $concedidas = '-';
        $cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_dl' => $id_dl, 'id_activ' => $id_activ));
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $dl_tabla = $oActividadPlazas->getDl_tabla();
            if ($dl_org == $dl_tabla) {
                $concedidas = $oActividadPlazas->getPlazas();
            } else {
                $pedidas = $oActividadPlazas->getPlazas();
            }
        }
        $dl_c = $dl . '-c';
        $dl_p = $dl . '-p';
        if ($mi_dl == $dl) {
            if ($mi_dl == $dl_org) {
                $a_valores[$i][$dl_c] = array('editable' => 'true', 'valor' => $concedidas);
                $a_valores[$i][$dl_p] = array('editable' => 'false', 'valor' => $pedidas);
            } else {
                $a_valores[$i][$dl_c] = array('editable' => 'false', 'valor' => $concedidas);
                $a_valores[$i][$dl_p] = array('editable' => 'true', 'valor' => $pedidas);
            }
        } else {
            if ($mi_dl == $dl_org) {
                $a_valores[$i][$dl_c] = array('editable' => 'true', 'valor' => $concedidas);
            } else {
                $a_valores[$i][$dl_c] = array('editable' => 'false', 'valor' => $concedidas);
            }
            $a_valores[$i][$dl_p] = array('editable' => 'false', 'valor' => $pedidas);
        }
        //$a_valores[$i][$c] = $pedidas;
    }

}
$a_cabeceras = array(
    array('name' => _("id_activ"), 'field' => 'id', 'visible' => 'no'),
    array('name' => _("actividad"), 'field' => 'actividad', 'width' => 200, 'formatter' => 'clickFormatter'),
    array('name' => _("org"), 'title' => _("organiza"), 'field' => "dlorg", 'width' => 40),
    array('name' => _("total"), 'title' => _("totales actividad"), 'field' => "tot", 'width' => 40, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter'),
);
foreach ($a_grupo as $dl => $id_dl) {
    /*
	$sub_cabecera = array(
		array('name'=>_("c"),'title'=>_("concedidas"),'field'=>$dl."-c",'width'=>15,'editor'=>'Slick.Editors.Integer'),
		array('name'=>_("p"),'title'=>_("pedidas"),'field'=>$dl."-p",'width'=>15,'editor'=>'Slick.Editors.Integer')
	);
	$a_cabeceras[] = array('name'=>$dl,'children'=>$sub_cabecera);
	*/
    $a_cabeceras[] = array('name' => $dl . '-c', 'title' => _("concedidas"), 'field' => $dl . "-c", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');
    $a_cabeceras[] = array('name' => $dl . '-p', 'title' => _("pedidas"), 'field' => $dl . "-p", 'width' => 15, 'editor' => 'Slick.Editors.Integer', 'formatter' => 'cssFormatter');
}
$a_botones = array();

$oTabla = new web\TablaEditable();
$oTabla->setId_tabla('gestion_plazas');
$UpdateUrl = core\ConfigGlobal::getWeb() . '/apps/actividadplazas/controller/gestion_plazas_ajax.php';
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

//Periodo
$boton = "<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >";
$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'curso_crt' => _("curso crt"),
    'separador1' => '---------',
    'otro' => _("otro")
);
$titulo = core\strtoupper_dlb(_("periodo de selección de actividades"));
$titulo .= " (" . _("en estado actual") . ")";
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo($titulo);
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setBoton($boton);

$oHash = new web\Hash();
$oHash->setCamposForm('empiezamax!empiezamin!iactividad_val!iasistentes_val!id_tipo_activ!periodo!year');
$oHash->setCamposNo('!refresh');
$a_camposHidden = array(
    'id_tipo_activ' => $Qid_tipo_activ,
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
];

$oView = new core\View('actividadplazas/controller');
$oView->renderizar('gestion_plazas.phtml', $a_campos);
