<?php

use actividades\model\entity\Actividad;
use actividades\model\entity\GestorActividad;
use actividadestudios\model\entity\GestorMatricula;
use personas\model\entity\GestorPersona;
use personas\model\entity\GestorPersonaStgr;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;
use web\DateTimeLocal;
use web\Hash;
use web\Lista;
use web\Periodo;
use web\Posicion;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
$oPosicion->recordar();

$aviso = '';
$form = '';
$traslados = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$Qapellidos = (string)filter_input(INPUT_POST, 'apellido1');

$a_botones = array(
    array('txt' => _("imprimir certificado"), 'click' => "fnjs_imp_certificado(this.form)"),
);

$a_cabeceras = array(
    _("alumno"),
    _("dl"),
    _("actividades"),
);

$titulo_busqueda_por_apellidos = _("búsqueda por apellidos");
$titulo = '';

////// Apellidos
if (!empty($Qapellidos)) {

    /*
    $GesPersonas = new GestorPersonaStgr();
    $cPersonas = $GesPersonas->getPerosnasOtrosStgr($Qapellidos);
    */
    $cPersonas = [];
    $i = 0;
    $a_Nombre = [];
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $dl = $oPersona->getDl();
        $apellidos_nombre = $oPersona->getPrefApellidosNombre();

        $i++;
        $a_valores[$i]['sel'] = "$id_nom";
        $a_valores[$i][1] = $apellidos_nombre;
        $a_valores[$i][2] = $dl;
        $a_valores[$i][3] = "";

        $a_Nombre[$i] = $apellidos_nombre;
    }
} else {
    ////// Periodo actividades

//periodo
    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_ca';
    }

// periodo.
    $oPeriodo = new Periodo();
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $inicioIso = $oPeriodo->getF_ini_iso();
    $finIso = $oPeriodo->getF_fin_iso();

    $aWhereActividad['f_ini'] = "'$inicioIso','$finIso'";
    $aOperadorActividad['f_ini'] = 'BETWEEN';

    $gesActividades = new GestorActividad();
    $a_IdActividades = $gesActividades->getArrayIds($aWhereActividad, $aOperadorActividad);

    $str_actividades = "{" . implode(', ', $a_IdActividades) . "}";
    $aWhere = ['id_activ' => $str_actividades];
    $aOperador = ['id_activ' => 'ANY'];

// Buscar dl y r dependientes de la actual región del stgr:
    $schema = $_SESSION['session_auth']['esquema'];
    $a_reg = explode('-', $schema);
    $RegionStgr = $a_reg[0];
    $gesDl = new GestorDelegacion();
    $a_dl_de_la_region_stgr = $gesDl->getArrayDlRegionStgr([$RegionStgr]);

    $gesMatriculasDl = new GestorMatricula();
    $a_alumnos_otras_regiones_stgr = $gesMatriculasDl->getMatriculasOtroStgr($a_IdActividades);

// Convertir las fechas inicio y fin a formato local:
    $oF_qini = new DateTimeLocal($inicioIso);
    $QinicioLocal = $oF_qini->getFromLocal();
    $oF_qfin = new DateTimeLocal($finIso);
    $QfinLocal = $oF_qfin->getFromLocal();
    $titulo = _(sprintf(_("Lista de alumnos de otras regiones matriculados en alguna asignatura en el periodo: %s - %s."), $QinicioLocal, $QfinLocal));
    $i = 0;
    $a_valores = array();
    $msg_err = '';
    $str_actividades = '';
    $id_nom_anterior = '';
    foreach ($a_alumnos_otras_regiones_stgr as $a_alumno_activ) {
        $i++;
        $id_nom = $a_alumno_activ['id_nom'];
        $id_activ = $a_alumno_activ['id_activ'];

        $oPersona = Persona::newPersona($id_nom);
        if (!is_object($oPersona)) {
            $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }

        $oActividad = new Actividad($id_activ);
        $nom_activ = $oActividad->getNom_activ();
        $dl_org = $oActividad->getDl_org();
        $str_actividades .= empty($str_actividades) ? '' : ', ';
        $str_actividades .= trim($nom_activ) . "($dl_org)";

        if ($id_nom != $id_nom_anterior) {
            $apellidos_nombre = $oPersona->getPrefApellidosNombre();
            $ctr = $oPersona->getCentro_o_dl();
            $dl = $oPersona->getDl();

            $a_valores[$i]['sel'] = "$id_nom";
            $a_valores[$i][1] = $apellidos_nombre;
            $a_valores[$i][2] = $dl;
            $a_valores[$i][3] = $str_actividades;

            $a_Nombre[$i] = $apellidos_nombre;
            $str_actividades = '';
        }
        $id_nom_anterior = $id_nom;
    }

}


// ordenar por alumno
if (!empty($a_valores)) {
    array_multisort(
        $a_Nombre, SORT_STRING,
        $a_valores);
}
//OJO!! hay añadirlos después de ordenar.
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new Hash();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);


if (!empty($msg_err)) {
    echo $msg_err;
}

$oTabla = new Lista();
$oTabla->setId_tabla('mtr_lista');
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
    'separador1' => '---------',
    'otro' => _("otro")
);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de selección de actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMax($Qempiezamax);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setBoton($boton);

$oHashPeriodo = new web\Hash();
$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHashPeriodo->setCamposNo('!refresh');
$a_camposHiddenP = array();
$oHashPeriodo->setArraycamposHidden($a_camposHiddenP);

$oHashApellidos = new web\Hash();
$oHashApellidos->setCamposForm('apellido1');
$a_camposHiddenP = array();
$oHashApellidos->setArraycamposHidden($a_camposHiddenP);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'mod' => $Qmod,
    'oTabla' => $oTabla,
    'titulo' => $titulo,
    'titulo_busqueda_por_apellidos' => $titulo_busqueda_por_apellidos,
    'aviso' => $aviso,
    'oFormP' => $oFormP,
    'oHashPeriodo' => $oHashPeriodo,
    'oHashApellidos' => $oHashApellidos,
];

$oView = new core\View('actividadestudios/controller');
$oView->renderizar('matriculas_otras_r.phtml', $a_campos);
