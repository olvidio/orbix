<?php
/**
 * Formulario de ver/editar una actividad (tambien para crear nueva o cambiar tipo).
 * Renderiza actividad_form.html.twig (en frontend/actividades/view).
 *
 * Migrado desde frontend/actividades/controller/actividad_ver.php.
 *
 * Los bloques que dependen del dominio (entidad actividad, desplegables,
 * nombre_ubi, tarifa por defecto) se obtienen via PostRequest al endpoint
 * backend /src/actividades/actividad_ver_datos. El controlador frontend
 * no accede directamente a `src/`.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use src\actividades\application\ActividadTipo;
use src\actividades\application\ActividadVerDatos;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use web\Hash;
use web\TiposActividades;
use function core\is_true;

require_once("frontend/shared/global_header_front.inc");

// Necesario cuando tengo que buscar el desplegable dl_org segun permisos en procesos
// (Como tambien afecta al status de la actividad, mejor rehacer toda la pagina).
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { // vengo de un checkbox
    $Qid_activ = (integer)strtok($a_sel[0], "#");
} else {
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$obj = 'actividades\\model\\entity\\ActividadAll';

$aQuery = array(
    'pau' => 'a',
    'id_pau' => $Qid_activ,
    'obj_pau' => $Qobj_pau,
);
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_des = TRUE;
}

$alt = '';
$dos = '';
$ssfsv = '';
$sasistentes = '';
$sactividad = '';
$snom_tipo = '';
$id_tipo_activ = '';
$nom_activ = '';
$f_ini = '';
$h_ini = '';
$f_fin = '';
$h_fin = '';
$plazas = '';
$idioma = '';
$precio = '';
$observ = '';
$publicado = '';
$lugar_esp = '';
$tarifa = '';
$id_repeticion = 0;
$nivel_stgr = NivelStgrId::N;
$id_ubi = 0;
$dl_org = '';
$status = 0;
$Bdl = 't';
$isfsv = 0;
$calc_tarifa_inicial = false;

if (!empty($Qid_activ)) { // caso de modificar
    $alt = _("ver dossiers");
    $dos = _("dossiers");
    if ($Qmod !== 'cambiar_tipo') {
        $Qmod = 'editar';
    }

    $a_status = StatusId::getArrayStatus(true);

    // Primera pasada: leemos solo la entidad para resolver permisos e isfsv.
    $dataEntidad = PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
        'id_activ' => $Qid_activ,
    ]);
    $entidad = $dataEntidad['entidad'] ?? null;
    if ($entidad === null) {
        die(_("No se encuentra la actividad"));
    }

    $id_tipo_activ = (string)$entidad['id_tipo_activ'];
    $dl_org = (string)$entidad['dl_org'];
    $nom_activ = (string)$entidad['nom_activ'];
    $id_ubi = (int)$entidad['id_ubi'];
    $f_ini = (string)$entidad['f_ini'];
    $h_ini = (string)$entidad['h_ini'];
    $f_fin = (string)$entidad['f_fin'];
    $h_fin = (string)$entidad['h_fin'];
    $precio = $entidad['precio'];
    $status = (int)$entidad['status'];
    $observ = (string)$entidad['observ'];
    $nivel_stgr = $entidad['nivel_stgr'] ?? NivelStgrId::N;
    $lugar_esp = (string)$entidad['lugar_esp'];
    $tarifa = $entidad['tarifa'];
    $id_repeticion = (int)$entidad['id_repeticion'];
    $publicado = $entidad['publicado'];
    $plazas = $entidad['plazas'];
    $idioma = (string)$entidad['idioma'];

    $_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

    if ($oPermActiv->only_perm('ocupado')) {
        die();
    }

    $oTipoActiv = new TiposActividades($id_tipo_activ);
    $ssfsv = $oTipoActiv->getSfsvText();
    $sasistentes = $oTipoActiv->getAsistentesText();
    $sactividad = $oTipoActiv->getActividadText();
    $snom_tipo = $oTipoActiv->getNom_tipoText();
    $isfsv = $oTipoActiv->getSfsvId();

    if (ConfigGlobal::is_app_installed('procesos')) {
        $Bdl = $oPermActiv->have_perm_activ('ver') ? 't' : 'f';
    }

    // Los fragmentos HTML de los desplegables y nombre_ubi ya vienen en
    // $dataEntidad; se computan con los valores reales de la actividad.
    $dataRender = $dataEntidad;
} else { // caso de nueva actividad
    $Qmod = 'nuevo';
    $isfsv = ConfigGlobal::mi_sfsv();

    $a_status = StatusId::getArrayStatus();
    $dl_org = ConfigGlobal::mi_delef();
    $status = StatusId::PROYECTO;
    $id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $id_tipo_activ = urldecode($id_tipo_activ); // En el caso de sr, sg, se pasa la cadena tipo 2[789]... (con [, que se encodan).

    if (!empty($id_tipo_activ)) {
        $isfsv = (integer)substr($id_tipo_activ, 0, 1);
        $calc_tarifa_inicial = true;
    }
    if (is_true($permiso_des)) {
        if (empty($id_tipo_activ)) {
            // valor por defecto. Si esta vacio dira que no tiene permiso.
            $id_tipo_activ = '1';
            $ssfsv = 'sv';
        }
        if ($isfsv == 1) {
            $ssfsv = 'sv';
            $dl_org = ConfigGlobal::mi_dele();
        }
        if ($isfsv == 2) {
            $ssfsv = 'sf';
            $dl_org = ConfigGlobal::mi_dele() . 'f';
        }
    } else {
        if ($isfsv == 1) {
            $ssfsv = 'sv';
        }
        if ($isfsv == 2) {
            $ssfsv = 'sf';
        }
    }

    $sasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $sactividad = (string)filter_input(INPUT_POST, 'sactividad');

    if (ConfigGlobal::is_app_installed('procesos')) {
        // Depende del proceso, para dl u otra
        // primera fase de los posibles procesos.
        // si no permiso para ninguno de los dos => die
        // si para dl, incluir la dl org
        // si dl_ex, idem.
        $_SESSION['oPermActividades']->setId_tipo_activ($id_tipo_activ);

        $crearPropia = $_SESSION['oPermActividades']->getPermisoCrear(TRUE);
        if ($crearPropia === FALSE) {
            echo '<br>';
            die (_("No tiene permiso para crear una actividad de este tipo"));
        }

        $of_responsable_txt = $crearPropia['of_responsable_txt'];
        if (!empty($of_responsable_txt) && $_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
            $Bdl = 't';
            $status = $crearPropia['status'];
        } else {
            if (empty($of_responsable_txt)) {
                $Bdl = 't';
                $status = $crearPropia['status'];
            } else {
                $Bdl = 'f';
                $crearEx = $_SESSION['oPermActividades']->getPermisoCrear(FALSE);
                $of_responsable_txt = $crearEx['of_responsable_txt'];
                $status = $crearEx['status'];
                if (!$_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
                    die (_("No tiene permiso para crear una actividad de este tipo"));
                }
            }
        }
    }

    // Para el permiso del boton guardar, en el caso de editar. Cuando es nuevo
    // no se utiliza. Se inicializa para que no de error.
    $oPermActiv = [];

    if (!empty($id_tipo_activ)) {
        $nivel_stgr = ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad($id_tipo_activ);
    }

    // Pedir desplegables + tarifa inicial para el caso 'nuevo'.
    $dataRender = PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
        'isfsv' => $isfsv,
        'dl_org' => $dl_org,
        'Bdl' => $Bdl,
        'tarifa' => '',
        'nivel_stgr' => $nivel_stgr,
        'idioma' => $idioma,
        'id_repeticion' => $id_repeticion,
        'id_ubi' => $id_ubi,
        'lugar_esp' => $lugar_esp,
        'id_tipo_activ' => $id_tipo_activ,
        'calc_tarifa_inicial' => $calc_tarifa_inicial ? 1 : 0,
    ]);
    if (!empty($dataRender['tarifa_inicial'])) {
        $tarifa = $dataRender['tarifa_inicial'];
    }
}

$html_despl_dl_org = (string)($dataRender['html_despl_dl_org'] ?? '');
$html_despl_tarifa = (string)($dataRender['html_despl_tarifa'] ?? '');
$html_despl_nivel_stgr = (string)($dataRender['html_despl_nivel_stgr'] ?? '');
$html_despl_idioma = (string)($dataRender['html_despl_idioma'] ?? '');
$html_despl_repeticion = (string)($dataRender['html_despl_repeticion'] ?? '');
$nombre_ubi = (string)($dataRender['nombre_ubi'] ?? '');

// En el caso 'nuevo' pedimos tarifa_inicial; si vino, hay que regenerar el
// desplegable de tarifa con la opcion ya seleccionada.
if (!empty($tarifa) && !empty($calc_tarifa_inicial)) {
    $dataTarifa = PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
        'isfsv' => $isfsv,
        'dl_org' => $dl_org,
        'Bdl' => $Bdl,
        'tarifa' => $tarifa,
        'nivel_stgr' => $nivel_stgr,
        'idioma' => $idioma,
        'id_repeticion' => $id_repeticion,
        'id_ubi' => $id_ubi,
        'lugar_esp' => $lugar_esp,
        'id_tipo_activ' => $id_tipo_activ,
        'calc_tarifa_inicial' => 0,
    ]);
    $html_despl_tarifa = (string)($dataTarifa['html_despl_tarifa'] ?? $html_despl_tarifa);
}

$oHash = new Hash();
$camposForm = 'status!dl_org!f_fin!f_ini!h_fin!h_ini!id_repeticion!id_ubi!lugar_esp!mod!nivel_stgr!nom_activ!nombre_ubi!observ!precio!id_tarifa!publicado!plazas!idioma';
$camposNo = 'mod!id_tarifa';
if ($Qmod === 'nuevo' || $Qmod === 'cambiar_tipo') {
    $camposForm .= '!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val';
    $camposNo .= '!id_tipo_activ';
} else {
    $camposForm .= '!sactividad!sasistentes!snom_tipo';
}
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo($camposNo);
$a_camposHidden = array(
    'id_tipo_activ' => $id_tipo_activ,
    'id_activ' => $Qid_activ,
    'ssfsv' => $ssfsv,
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/frontend/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv!isfsv');
$h = $oHash1->linkSinVal();

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setSfsvAll(TRUE);
$oActividadTipo->setQue('buscar');
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);
$extendida = (($Qmod !== 'cambiar_tipo') && (strlen($id_tipo_activ) > 3));

$procesos_installed = ConfigGlobal::is_app_installed('procesos');

$status_txt = $a_status[$status] ?? '';
$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'obj' => addslashes($obj),
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'oPermActiv' => $oPermActiv,
    'permiso_des' => $permiso_des,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'snom_tipo' => $snom_tipo,
    'ssfsv' => $ssfsv,
    'status' => $status,
    'status_txt' => $status_txt,
    'nom_activ' => $nom_activ,
    'f_ini' => $f_ini,
    'h_ini' => $h_ini,
    'f_fin' => $f_fin,
    'h_fin' => $h_fin,
    // Fragmentos HTML de los desplegables resueltos en backend.
    'html_despl_dl_org' => $html_despl_dl_org,
    'html_despl_tarifa' => $html_despl_tarifa,
    'html_despl_nivel_stgr' => $html_despl_nivel_stgr,
    'html_despl_idioma' => $html_despl_idioma,
    'html_despl_repeticion' => $html_despl_repeticion,
    'plazas' => $plazas,
    'nombre_ubi' => $nombre_ubi,
    'id_ubi' => $id_ubi,
    'lugar_esp' => $lugar_esp,
    'precio' => $precio,
    'observ' => $observ,
    'publicado' => $publicado,
    'mod' => $Qmod,
    'oActividadTipo' => $oActividadTipo,
    'extendida' => $extendida,
    'id_tipo_activ' => $id_tipo_activ,
    'web' => ConfigGlobal::getWeb(),
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'procesos_installed' => $procesos_installed,
    'locale_us' => ConfigGlobal::is_locale_us(),
];

$oView = new ViewNewTwig('actividades/controller');
$oView->renderizar('actividad_form.html.twig', $a_campos);
