<?php
/**
 * Controlador AJAX HTML: form modificar/nuevo de
 * `RelacionTarifaTipoActividad`.
 *
 * Obtiene los datos de `/src/actividadtarifas/relacion_tarifa_form_data`
 * y renderiza `tarifa_tipo_actividad_form.html.twig` (modificar) o
 * `tarifa_tipo_actividad_form_nuevo.html.twig` (nuevo). Ambos reusan
 * `ActividadTipo::getHtml()` para los desplegables de tipo de
 * actividad.
 *
 * Sucesor de
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad_form.php`.
 */

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use src\actividades\application\ActividadTipo;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;

require_once 'frontend/shared/global_header_front.inc';

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$campos = ['id_item' => $Qid_item];
$data = PostRequest::getDataFromUrl('/src/actividadtarifas/relacion_tarifa_form_data', $campos);
$payload = is_array($data) ? $data : [];

$es_nuevo = (bool)($payload['es_nuevo'] ?? true);
$id_item = (string)($payload['id_item'] ?? 'nuevo');
$id_tipo_activ = (int)($payload['id_tipo_activ'] ?? 0);
$id_tarifa_sel = (int)($payload['id_tarifa_sel'] ?? 0);
$isfsv = (int)($payload['isfsv'] ?? 0);
$opciones_tarifa = $payload['opciones_tarifa'] ?? [];

$oDesplPosiblesTipoTarifas = new Desplegable();
$oDesplPosiblesTipoTarifas->setNombre('id_tarifa');
$oDesplPosiblesTipoTarifas->setOpciones($opciones_tarifa);
if (!$es_nuevo) {
    $oDesplPosiblesTipoTarifas->setOpcion_sel($id_tarifa_sel);
}

$web = rtrim(ConfigGlobal::getWeb(), '/');

// Hash para el form (campos que se serializan en el submit):
$oHash = new Hash();
$a_camposHidden = [];
if ($es_nuevo) {
    $oHash->setUrl($web . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('id_item!id_tarifa!id_tipo_activ!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val');
    $a_camposHidden = [
        'id_item' => 'nuevo',
        'id_tipo_activ' => '',
    ];
} else {
    $oHash->setUrl($web . '/src/actividadtarifas/relacion_tarifa_update');
    $oHash->setCamposForm('id_item!id_tarifa!id_tipo_activ');
    $a_camposHidden = [
        'id_item' => $id_item,
        'id_tipo_activ' => (string)$id_tipo_activ,
    ];
}
$oHash->setArraycamposHidden($a_camposHidden);

if (!$es_nuevo) {
    $oTipoActiv = new TiposActividades($id_tipo_activ);
    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'oTipoActiv' => $oTipoActiv,
        'extendida' => false,
        'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
        'locale_us' => ConfigGlobal::is_locale_us(),
    ];

    $oView = new ViewNewTwig('actividadtarifas/controller');
    $oView->renderizar('tarifa_tipo_actividad_form.html.twig', $a_campos);
} else {
    $oActividadTipo = new ActividadTipo();
    $oActividadTipo->setSfsv((string)$isfsv);
    $oActividadTipo->setPara('tipoactiv-tarifas');

    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
        'oActividadTipo' => $oActividadTipo,
    ];

    $oView = new ViewNewTwig('actividadtarifas/controller');
    $oView->renderizar('tarifa_tipo_actividad_form_nuevo.html.twig', $a_campos);
}
