<?php

use frontend\actividades\helpers\ActividadesPermSupport;
use frontend\actividades\helpers\ActividadesPostInput;
use frontend\actividades\helpers\ActividadesPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Formulario de ver/editar una actividad (tambien para crear nueva o cambiar tipo).
 * Renderiza actividad_form.html.twig (en frontend/actividades/view).
 *
 * Migrado desde frontend/actividades/controller/actividad_ver.php.
 *
 * Los bloques que dependen del dominio (entidad actividad, desplegables,
 * nombre_ubi, tarifa por defecto, textos del tipo, etiquetas de status,
 * nivel STGR por defecto, HTML del bloque tipo, permiso crear en flujo nuevo)
 * se obtienen via PostRequest a `/src/actividades/actividad_ver_datos`,
 * `actividad_status_labels_datos`, `actividad_nivel_stgr_default_datos`,
 * `actividad_permiso_crear_datos` y `actividad_que_datos`. El controlador
 * frontend no hace `use src\...`.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use frontend\actividades\helpers\ActividadStatusId;
use frontend\actividades\helpers\NivelStgrId;
use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\actividades\helpers\PrefillPermActividadesFases;
use frontend\shared\FrontBootstrap;
use src\permisos\domain\PermisosActividades;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$Qid_activ = ActividadesPostInput::idActivFromPost();

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $Qid_activ > 0 ? ['id_activ' => $Qid_activ] : [],
    ListNavSupport::buildActividadVerReturnParametros([
        'mod' => $Qmod,
        'obj_pau' => $Qobj_pau,
        'id_activ' => $Qid_activ > 0 ? $Qid_activ : null,
        'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
        'ssfsv' => (string)filter_input(INPUT_POST, 'ssfsv'),
        'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
        'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
        'snom_tipo' => (string)filter_input(INPUT_POST, 'snom_tipo'),
        'id_sel' => ListNavSupport::idSelFromPost(),
        'scroll_id' => ListNavSupport::scrollIdFromPost(),
    ]),
);

ListNavSupport::syncActividadSelectParentSelection($oPosicion);

$obj = 'actividades\\model\\entity\\ActividadAll';

$aQuery = array(
    'pau' => 'a',
    'id_pau' => $Qid_activ,
    'obj_pau' => $Qobj_pau,
);
array_walk($aQuery, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
$godossiers = HashFront::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$permiso_des = ActividadesPermSupport::permDes();

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
$dataRender = [];

if (!empty($Qid_activ)) { // caso de modificar
    $alt = _("ver dossiers");
    $dos = _("dossiers");
    if ($Qmod !== 'cambiar_tipo') {
        $Qmod = 'editar';
    }

    $labelsRow = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_status_labels_datos', [
        'with_all' => 't',
    ]));
    $a_status = ActividadesPayload::statusLabelsFromPayload($labelsRow);

    // Primera pasada: leemos solo la entidad para resolver permisos e isfsv.
    $dataEntidad = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
        'id_activ' => $Qid_activ,
    ]));
    $entidad = ActividadesPayload::entidadFromVerDatos($dataEntidad);

    $id_tipo_activ = PayloadCoercion::string($entidad['id_tipo_activ'] ?? '');
    $dl_org = PayloadCoercion::string($entidad['dl_org'] ?? '');
    $nom_activ = $entidad['nom_activ'];
    $id_ubi = $entidad['id_ubi'];
    $f_ini = $entidad['f_ini'];
    $h_ini = $entidad['h_ini'];
    $f_fin = $entidad['f_fin'];
    $h_fin = $entidad['h_fin'];
    $precio = $entidad['precio'];
    $status = $entidad['status'];
    $observ = $entidad['observ'];
    $nivel_stgr = $entidad['nivel_stgr'] !== '' ? $entidad['nivel_stgr'] : NivelStgrId::N;
    $lugar_esp = $entidad['lugar_esp'];
    $tarifa = $entidad['tarifa'];
    $id_repeticion = $entidad['id_repeticion'];
    $publicado = $entidad['publicado'];
    $plazas = $entidad['plazas'];
    $idioma = $entidad['idioma'];

    $oPermActividades = ActividadesPermSupport::oPermActividades();
    if (!$oPermActividades instanceof PermisosActividades) {
        die();
    }
    $oPermActividades->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
    PrefillPermActividadesFases::desdeBackend($Qid_activ);
    $oPermActiv = $oPermActividades->getPermisoActual('datos');

    if ($oPermActiv->only_perm('ocupado')) {
        die();
    }

    $renderEntidad = ActividadesPayload::verRenderFromPayload($dataEntidad);
    $ssfsv = $renderEntidad['ssfsv'];
    $sasistentes = $renderEntidad['sasistentes'];
    $sactividad = $renderEntidad['sactividad'];
    $snom_tipo = $renderEntidad['snom_tipo'];
    $isfsv = $renderEntidad['isfsv'];

    if (AppInstalled::is('procesos')) {
        $Bdl = $oPermActiv->have_perm_activ('ver') ? 't' : 'f';
    }

    // Los fragmentos HTML de los desplegables y nombre_ubi ya vienen en
    // $dataEntidad; se computan con los valores reales de la actividad.
    $dataRender = $dataEntidad;
} else { // caso de nueva actividad
    $Qmod = 'nuevo';
    $isfsv = OrbixRuntime::miSfsv();

    $labelsRow = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_status_labels_datos', [
        'with_all' => 'f',
    ]));
    $a_status = ActividadesPayload::statusLabelsFromPayload($labelsRow);
    $dl_org = OrbixRuntime::miDelef();
    $status = ActividadStatusId::PROYECTO;
    $id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $id_tipo_activ = urldecode($id_tipo_activ); // En el caso de sr, sg, se pasa la cadena tipo 2[789]... (con [, que se encodan).

    if (!empty($id_tipo_activ)) {
        $isfsv = (integer)substr($id_tipo_activ, 0, 1);
        $calc_tarifa_inicial = true;
    }
    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($permiso_des)) {
        if (empty($id_tipo_activ)) {
            // valor por defecto. Si esta vacio dira que no tiene permiso.
            $id_tipo_activ = '1';
            $ssfsv = 'sv';
        }
        if ($isfsv == 1) {
            $ssfsv = 'sv';
            $dl_org = OrbixRuntime::miDele();
        }
        if ($isfsv == 2) {
            $ssfsv = 'sf';
            $dl_org = OrbixRuntime::miDele() . 'f';
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

    if (AppInstalled::is('procesos')) {
        // Depende del proceso, para dl u otra
        // primera fase de los posibles procesos.
        // si no permiso para ninguno de los dos => die
        // si para dl, incluir la dl org
        // si dl_ex, idem.
        // Resolver vía backend: el frontend no tiene $GLOBALS['container'].
        $rowPropia = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_permiso_crear_datos', [
            'id_tipo_activ' => $id_tipo_activ,
            'dl_propia' => 't',
        ]));
        $crearPropia = ActividadesPayload::permisoCrearFromRow($rowPropia);
        if (!empty($rowPropia['aviso'])) {
            echo '<br>' . \frontend\shared\helpers\PayloadCoercion::string($rowPropia['aviso']);
        }
        if ($crearPropia === null) {
            echo '<br>';
            die (_("No tiene permiso para crear una actividad de este tipo"));
        }

        $oPerm = ActividadesPermSupport::oPerm();
        $of_responsable_txt = $crearPropia['of_responsable_txt'];
        if (!empty($of_responsable_txt) && $oPerm !== null && $oPerm->have_perm_oficina($of_responsable_txt)) {
            $Bdl = 't';
            $status = $crearPropia['status'];
        } else {
            if (empty($of_responsable_txt)) {
                $Bdl = 't';
                $status = $crearPropia['status'];
            } else {
                $Bdl = 'f';
                $rowEx = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_permiso_crear_datos', [
                    'id_tipo_activ' => $id_tipo_activ,
                    'dl_propia' => 'f',
                ]));
                $crearEx = ActividadesPayload::permisoCrearFromRow($rowEx);
                if (!empty($rowEx['aviso'])) {
                    echo '<br>' . \frontend\shared\helpers\PayloadCoercion::string($rowEx['aviso']);
                }
                if ($crearEx === null) {
                    die (_("No tiene permiso para crear una actividad de este tipo"));
                }
                $of_responsable_txt = $crearEx['of_responsable_txt'];
                $status = $crearEx['status'];
                if ($oPerm === null || !$oPerm->have_perm_oficina($of_responsable_txt)) {
                    die (_("No tiene permiso para crear una actividad de este tipo"));
                }
            }
        }
    }

    // Para el permiso del boton guardar, en el caso de editar. Cuando es nuevo
    // no se utiliza. Se inicializa para que no de error.
    $oPermActiv = [];

    if (!empty($id_tipo_activ)) {
        $dataNivelDef = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_nivel_stgr_default_datos', [
            'id_tipo_activ' => $id_tipo_activ,
        ]));
        $nivel_stgr = \frontend\shared\helpers\PayloadCoercion::int($dataNivelDef['nivel_stgr_default'] ?? 9);
    }

    // Pedir desplegables + tarifa inicial para el caso 'nuevo'.
    $dataRender = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
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
    ]));
    if (!empty($dataRender['tarifa_inicial'])) {
        $tarifa = $dataRender['tarifa_inicial'];
    }
}

$renderForm = ActividadesPayload::verRenderFromPayload($dataRender);
$html_despl_dl_org = $renderForm['html_despl_dl_org'];
$html_despl_tarifa = $renderForm['html_despl_tarifa'];
$html_despl_nivel_stgr = $renderForm['html_despl_nivel_stgr'];
$html_despl_idioma = $renderForm['html_despl_idioma'];
$html_despl_repeticion = $renderForm['html_despl_repeticion'];
$nombre_ubi = $renderForm['nombre_ubi'];

// En el caso 'nuevo' pedimos tarifa_inicial; si vino, hay que regenerar el
// desplegable de tarifa con la opcion ya seleccionada.
if (!empty($tarifa) && !empty($calc_tarifa_inicial)) {
    $dataTarifa = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
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
    ]));
    $renderTarifa = ActividadesPayload::verRenderFromPayload($dataTarifa);
    if ($renderTarifa['html_despl_tarifa'] !== '') {
        $html_despl_tarifa = $renderTarifa['html_despl_tarifa'];
    }
}

$oHash = new HashFront();
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

$oHash1 = new HashFront();
$oHash1->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv!isfsv');
$h = $oHash1->linkSinVal();

$extendida = ($Qmod !== 'cambiar_tipo') && (strlen(PayloadCoercion::string($id_tipo_activ)) > 3);
$dataTipoBloque = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_que_datos', [
    'perm_jefe' => $permiso_des ? 't' : 'f',
    'id_tipo_activ' => PayloadCoercion::string($id_tipo_activ),
    'que' => 'buscar',
    'sfsv' => $ssfsv,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'sactividad2' => '',
    'snom_tipo' => $snom_tipo,
    'extendida' => $extendida ? 't' : '',
]));
$actividad_tipo_html = PayloadCoercion::string($dataTipoBloque['actividad_tipo_html'] ?? '');

$procesos_installed = AppInstalled::is('procesos');

$status_txt = $a_status[PayloadCoercion::int($status)] ?? '';
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
    'actividad_tipo_html' => $actividad_tipo_html,
    'extendida' => $extendida,
    'id_tipo_activ' => $id_tipo_activ,
    'web' => AppUrlConfig::getPublicAppBaseUrl(),
    'web_icons' => OrbixRuntime::getWebIcons(),
    'procesos_installed' => $procesos_installed,
    'locale_us' => OrbixRuntime::isLocaleUs(),
];

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('actividad_form.html.twig', $a_campos);
