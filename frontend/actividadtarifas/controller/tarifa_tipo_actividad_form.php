<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\actividadtarifas\helpers\ActividadtarifasPayload;

/**
 * Controlador AJAX HTML: form modificar/nuevo de
 * `RelacionTarifaTipoActividad`.
 *
 * Obtiene los datos de `/src/actividadtarifas/relacion_tarifa_form_data`
 * y renderiza `tarifa_tipo_actividad_form.html.twig` (modificar) o
 * `tarifa_tipo_actividad_form_nuevo.html.twig` (nuevo). El bloque de
 * desplegables del modo «nuevo» viene de `/src/actividades/actividad_que_datos`
 * (PostRequest). Sin `use src\...` en el controlador frontend.
 *
 * Sucesor de
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad_form.php`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$campos = ['id_item' => $Qid_item];
$fields = ActividadtarifasPayload::fields(
    PostRequest::getDataFromUrl('/src/actividadtarifas/relacion_tarifa_form_data', $campos)
);

$es_nuevo = $fields['es_nuevo'];
$id_item = $fields['id_item'];
$id_tipo_activ = $fields['id_tipo_activ'];
$id_tarifa_sel = $fields['id_tarifa_sel'];
$isfsv = $fields['isfsv'];
$nom_tipo_activ = $fields['nom_tipo_activ'];

$oDesplPosiblesTipoTarifas = new Desplegable();
$oDesplPosiblesTipoTarifas->setNombre('id_tarifa');
$oDesplPosiblesTipoTarifas->setOpciones($fields['opciones_tarifa']);
if (!$es_nuevo) {
    $oDesplPosiblesTipoTarifas->setOpcion_sel(\frontend\shared\helpers\PayloadCoercion::string($id_tarifa_sel));
}

$api = AppUrlConfig::getApiBaseUrl();

// Hash para el form (campos que se serializan en el submit):
$oHash = new HashFront();
$a_camposHidden = [];
if ($es_nuevo) {
    $oHash->setUrl($api . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('id_item!id_tarifa!id_tipo_activ!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val');
    $a_camposHidden = [
        'id_item' => 'nuevo',
        'id_tipo_activ' => '',
    ];
} else {
    $oHash->setUrl($api . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('id_item!id_tarifa!id_tipo_activ');
    $a_camposHidden = [
        'id_item' => $id_item,
        'id_tipo_activ' => (string)$id_tipo_activ,
    ];
}
$oHash->setArraycamposHidden($a_camposHidden);

if (!$es_nuevo) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'nom_tipo_activ' => $nom_tipo_activ,
        'extendida' => false,
        'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
        'locale_us' => OrbixRuntime::isLocaleUs(),
    ];

    $oView = new ViewNewTwig('frontend/actividadtarifas/controller');
    $oView->renderizar('tarifa_tipo_actividad_form.html.twig', $a_campos);
} else {
    $ssfsv = '';
    if ($isfsv === 1) {
        $ssfsv = 'sv';
    }
    if ($isfsv === 2) {
        $ssfsv = 'sf';
    }
    $dataTipo = PostRequest::getDataFromUrl('/src/actividades/actividad_que_datos', [
        'perm_jefe' => 'f',
        'id_tipo_activ' => '',
        'que' => '',
        'para' => 'tipoactiv-tarifas',
        'sfsv' => $ssfsv,
        'sasistentes' => '',
        'sactividad' => '',
        'sactividad2' => '',
        'snom_tipo' => '',
        'extendida' => '',
        'sfsv_all' => 'f',
    ]);
    $actividad_tipo_html = \frontend\shared\helpers\PayloadCoercion::string($dataTipo['actividad_tipo_html'] ?? '');

    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
        'actividad_tipo_html' => $actividad_tipo_html,
    ];

    $oView = new ViewNewTwig('frontend/actividadtarifas/controller');
    $oView->renderizar('tarifa_tipo_actividad_form_nuevo.html.twig', $a_campos);
}
