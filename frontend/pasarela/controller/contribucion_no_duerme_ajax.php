<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\actividades\helpers\ActividadTipo;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\pasarela\helpers\PasarelaPayload;
use frontend\pasarela\helpers\PasarelaExcepcionRender;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/**
 * Dispatcher AJAX para el parámetro `contribucion_no_duerme`.
 *
 * Misma estructura que `activacion_ajax.php`: cada `que` delega en un endpoint
 * `/src/pasarela/contribucion_no_duerme_*` vía `PostRequest::getDataFromUrl(...)`
 * y, si toca, monta el HTML/Twig en frontend.
 */

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/contribucion_no_duerme_ajax.php';

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'eliminar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        PostRequest::getDataFromUrl('/src/pasarela/contribucion_no_duerme_excepcion_eliminar', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        break;
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qcontribucion = (string)filter_input(INPUT_POST, 'contribucion');
        PostRequest::getDataFromUrl('/src/pasarela/contribucion_no_duerme_excepcion_guardar', [
            'id_tipo_activ' => $Qid_tipo_activ,
            'valor' => $Qcontribucion,
        ]);
        break;
    case 'update_default':
        $Qdefault = (string)filter_input(INPUT_POST, 'default');
        PostRequest::getDataFromUrl('/src/pasarela/contribucion_no_duerme_default_guardar', [
            'default' => $Qdefault,
        ]);
        break;
    case 'lista':
        $data = PostRequest::getDataFromUrl('/src/pasarela/contribucion_no_duerme_lista');
        $lista = PasarelaPayload::excepcionListaConDefaultFromPayload($data);
        echo PasarelaExcepcionRender::listaConDefaultHtml($lista, 'fnjs_modificar_default()', 'fnjs_modificar');
        break;
    case 'form_default':
        $data = PostRequest::getDataFromUrl('/src/pasarela/contribucion_no_duerme_default_data');
        $default = PayloadCoercion::string($data['default'] ?? '');
        $txt = _('Valor por defecto en %');

        $oHash = new HashFront();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('default');
        $oHash->setArrayCamposHidden(['que' => 'update_default']);

        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'default' => $default,
            'txt' => $txt,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        $oView->renderizar('contribucion_x_default_form.html.twig', $a_campos);
        break;
    case 'form_modificar':
        $txt = _('Contribución si no duerme en % para');
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qcontribucion = (string)filter_input(INPUT_POST, 'contribucion');

        $data = PostRequest::getDataFromUrl('/src/pasarela/tipo_activ_txt_data', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        $tipo_txt = PasarelaPayload::tipoTxtFromPayload($data);

        $oHash = new HashFront();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('id_tipo_activ!contribucion');
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
            'contribucion' => $Qcontribucion,
            'txt' => $txt,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        $oView->renderizar('contribucion_x_form.html.twig', $a_campos);
        break;
    case 'form_nuevo':
        $txt = _('Contribución si no duerme en %');
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
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!contribucion');
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
            'txt' => $txt,
        ];

        $oView = new ViewNewTwig('frontend/pasarela/controller');
        $oView->renderizar('contribucion_x_form_nuevo.html.twig', $a_campos);
        break;
}
