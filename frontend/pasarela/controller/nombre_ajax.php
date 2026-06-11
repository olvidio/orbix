<?php

use frontend\actividades\helpers\ActividadTipo;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/pasarela_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

$oPosicion = FrontBootstrap::boot();
/**
 * Dispatcher AJAX para el parámetro `nombre`.
 *
 * Misma estructura que `activacion_ajax.php`: cada `que` delega en un endpoint
 * `/src/pasarela/nombre_*` vía `PostRequest::getDataFromUrl(...)`. El parámetro
 * `nombre` no tiene valor por defecto, así que solo expone lista, alta,
 * actualización y eliminación de excepciones.
 */

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/nombre_ajax.php';

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'eliminar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        ajax_json_proxy_post_request('/src/pasarela/nombre_excepcion_eliminar', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qnombre_actividad = (string)filter_input(INPUT_POST, 'nombre_actividad');
        ajax_json_proxy_post_request('/src/pasarela/nombre_excepcion_guardar', [
            'id_tipo_activ' => $Qid_tipo_activ,
            'valor' => $Qnombre_actividad,
        ]);
    case 'lista':
        $data = PostRequest::getDataFromUrl('/src/pasarela/nombre_lista');
        $lista = pasarela_excepcion_lista_from_payload($data);
        ajax_json_html(pasarela_render_excepcion_lista_html($lista, 'fnjs_modificar'));
    case 'form_modificar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qnombre_actividad = (string)filter_input(INPUT_POST, 'nombre_actividad');

        $data = PostRequest::getDataFromUrl('/src/pasarela/tipo_activ_txt_data', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        $tipo_txt = pasarela_tipo_txt_from_payload($data);

        $oHash = new HashFront();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('id_tipo_activ!nombre_actividad');
        $oHash->setCamposNo('id_tipo_activ!que');
        $oHash->setArrayCamposHidden([
            'id_tipo_activ' => $Qid_tipo_activ,
            'que' => '',
        ]);

        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'tipo_txt' => $tipo_txt,
            'nombre_actividad' => $Qnombre_actividad,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        ob_start();
        $oView->renderizar('nombre_form.html.twig', $a_campos);
        ajax_json_html((string) ob_get_clean());
    case 'form_nuevo':
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
        $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
        $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

        $oActividadTipo = new ActividadTipo();
        $oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
        $oActividadTipo->setAsistentes($Qsasistentes);
        $oActividadTipo->setActividad($Qsactividad);
        $oActividadTipo->setNom_tipo($Qsnom_tipo);
        $oActividadTipo->setPara('tipoactiv-tarifas');

        $oHash = new HashFront();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!nombre_actividad');
        $oHash->setCamposNo('id_tipo_activ!que');
        $oHash->setArrayCamposHidden([
            'id_tipo_activ' => '',
            'que' => '',
        ]);

        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'oActividadTipo' => $oActividadTipo,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        ob_start();
        $oView->renderizar('nombre_form_nuevo.html.twig', $a_campos);
        ajax_json_html((string) ob_get_clean());
}
