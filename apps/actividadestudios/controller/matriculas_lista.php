<?php

use core\ViewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\services\TelecoPersonaService;
use web\DateTimeLocal;
use web\Hash;
use web\Lista;
use web\Periodo;
use web\Posicion;
use function core\is_true;

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
    if ($stack !== '') {
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

$ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
$a_IdActividades = $ActividadRepository->getArrayIdsWithKeyFini($aWhereActividad, $aOperadorActividad);

$str_actividades = "{" . implode(', ', $a_IdActividades) . "}";
$aWhere = ['id_activ' => $str_actividades];
$aOperador = ['id_activ' => 'ANY'];

$MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
$cMatriculas = $MatriculaDlRepository->getMatriculas($aWhere, $aOperador);

// Convertir las fechas inicio y fin a formato local:
$oF_qini = new DateTimeLocal($inicioIso);
$QinicioLocal = $oF_qini->getFromLocal();
$oF_qfin = new DateTimeLocal($finIso);
$QfinLocal = $oF_qfin->getFromLocal();
$titulo = _(sprintf(_("Lista de matrículas en el periodo: %s - %s."), $QinicioLocal, $QfinLocal));
$a_botones = array(
    array('txt' => _("ver asignaturas ca"), 'click' => "fnjs_ver_ca(this.form)"),
    array('txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form)")
);

$a_cabeceras = array(
    _("alumno"),
    _("ctr"),
    _("dl"),
    _("actividad"),
    _("asignatura"),
    _("preceptor"),
    _("nota")
);

$i = 0;
$a_valores = [];
$msg_err = '';
$id_nom_anterior = '';
$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
foreach ($cMatriculas as $oMatricula) {
    $i++;
    $id_nom = $oMatricula->getId_nom();
    $id_activ = $oMatricula->getId_activ();
    $id_asignatura = $oMatricula->getId_asignatura();
    $nota_txt = $oMatricula->getNotaSobre();
    $preceptor = $oMatricula->isPreceptor();
    if (is_true($preceptor)) {
        $preceptor = 'x';
        $id_preceptor = $oMatricula->getId_preceptor();
        $mails_preceptor = '';
        if (!empty($id_preceptor)) {
            $oPersona = Persona::findPersonaEnGlobal($id_preceptor);
            if ($oPersona === null) {
                $msg_err .= "<br>preceptor: No encuentro a nadie con id_nom: $id_preceptor en  " . __FILE__ . ": line " . __LINE__;
            } else {
                $telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
                $preceptor = $oPersona->getPrefApellidosNombre();
                $mails_preceptor = $telecoService->getTelecosPorTipo($id_preceptor, 'e-mail', ' / ');
                if (!empty($mails_preceptor)) {
                    $preceptor .= ' [' . $mails_preceptor . ']';
                }
            }
        }
    } else {
        $preceptor = "";
    }

    //echo "id_activ: $id_activ<br>";
    //echo "id_asignatura: $id_asignatura<br>";

    $oActividad = $ActividadAllRepository->findById($id_activ);
    $nom_activ = $oActividad->getNom_activ();

    if ($id_nom !== $id_nom_anterior) {
        $mails_alumno = '';
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }
        $telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
        $apellidos_nombre = $oPersona->getPrefApellidosNombre();
        $ctr = $oPersona->getCentro_o_dl();
        $dl = $oPersona->getDl();
        $mails_alumno = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', ' / ');
        if (!empty($mails_alumno)) {
            $apellidos_nombre .= ' [' . $mails_alumno . ']';
        }
    }

    $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
    if ($oAsignatura === null) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();

    $a_valores[$i]['sel'] = "$id_activ#$id_asignatura#$id_nom";
    $a_valores[$i][1] = $apellidos_nombre;
    $a_valores[$i][2] = $ctr;
    $a_valores[$i][3] = $dl;
    $a_valores[$i][4] = $nom_activ;
    $a_valores[$i][5] = $nombre_corto;
    $a_valores[$i][6] = $preceptor;
    $a_valores[$i][7] = $nota_txt;

    $a_Nombre[$i] = $apellidos_nombre;
    $a_Asignatura[$i] = $nombre_corto;

    $id_nom_anterior = $id_nom;
}

// ordenar por alumno, asignatura:
if (!empty($a_valores)) {
    array_multisort(
        $a_Nombre, SORT_STRING,
        $a_Asignatura, SORT_STRING,
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

$txt_eliminar = _("¿Está seguro que desea borrar todas las matrículas seleccionadas?");

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

$oHashPeriodo = new Hash();
$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHashPeriodo->setCamposNo('!refresh');
$a_camposHiddenP = [];
$oHashPeriodo->setArraycamposHidden($a_camposHiddenP);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'mod' => $Qmod,
    'oTabla' => $oTabla,
    'titulo' => $titulo,
    'aviso' => $aviso,
    'txt_eliminar' => $txt_eliminar,
    'oFormP' => $oFormP,
    'oHashPeriodo' => $oHashPeriodo,
];

$oView = new ViewPhtml('actividadestudios\controller');
$oView->renderizar('matriculas.phtml', $a_campos);
