<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\actividades\helpers\ActividadTipo;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
use frontend\pasarela\helpers\PasarelaPayload;
use frontend\pasarela\helpers\PasarelaExcepcionRender;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/**
 * Dispatcher AJAX para el parámetro `fecha_activacion`.
 *
 * El backend vive en `src/pasarela/infrastructure/ui/http/controllers/`; aquí
 * solo orquestamos: leemos `que`, llamamos al endpoint correspondiente con
 * `PostRequest::getDataFromUrl(...)` y pintamos HTML/Twig en frontend.
 *
 * Contrato con el JS de las plantillas Twig:
 *  - mutaciones (`eliminar`, `update`, `nuevo`, `update_default`): JSON
 *    `{"success":true}` si éxito; si falla el POST interno,
 *    `PostRequest::getDataFromUrl` hace `exit()` con el mensaje (no JSON).
 *  - `lista`: HTML para `#div_tabla`.
 *  - `form_*`: HTML del formulario para `#div_modificar`.
 */

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/activacion_ajax.php';

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'eliminar':
        $Qctx_eliminar = (string)filter_input(INPUT_POST, 'ctx_eliminar');
        AjaxJsonSupport::proxyPostRequest('/src/pasarela/activacion_excepcion_eliminar', [
            'ctx_eliminar' => $Qctx_eliminar,
        ]);
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qactivacion = (string)filter_input(INPUT_POST, 'activacion');
        $Qctx_guardar = (string)filter_input(INPUT_POST, 'ctx_guardar');
        AjaxJsonSupport::proxyPostRequest('/src/pasarela/activacion_excepcion_guardar', [
            'ctx_guardar' => $Qctx_guardar,
            'id_tipo_activ' => $Qid_tipo_activ,
            'valor' => $Qactivacion,
        ]);
    case 'update_default':
        $Qdefault = (string)filter_input(INPUT_POST, 'default');
        $Qctx_guardar = (string)filter_input(INPUT_POST, 'ctx_guardar');
        AjaxJsonSupport::proxyPostRequest('/src/pasarela/activacion_default_guardar', [
            'default' => $Qdefault,
            'ctx_guardar' => $Qctx_guardar,
        ]);
    case 'lista':
        $data = PostRequest::getDataFromUrl('/src/pasarela/activacion_lista');
        $lista = PasarelaPayload::excepcionListaConDefaultFromPayload($data);
        AjaxJsonSupport::html(PasarelaExcepcionRender::listaConDefaultHtml($lista, 'fnjs_modificar_activacion_default()', 'fnjs_modificar_activacion'));
    case 'form_default':
        $data = PostRequest::getDataFromUrl('/src/pasarela/activacion_default_data');
        $default = \frontend\shared\helpers\PayloadCoercion::string($data['default'] ?? '');
        $ctx_guardar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_guardar'] ?? '');

        $oHash = new HashF();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('default');
        $oHash->setArrayCamposHidden(['que' => 'update_default', 'ctx_guardar' => $ctx_guardar]);
        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'default' => $default,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        ob_start();
        $oView->renderizar('activacion_default_form.html.twig', $a_campos);
        AjaxJsonSupport::html((string) ob_get_clean());
    case 'form_modificar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qactivacion = (string)filter_input(INPUT_POST, 'activacion');

        $data = PostRequest::getDataFromUrl('/src/pasarela/tipo_activ_txt_data', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        $tipo_txt = PasarelaPayload::tipoTxtFromPayload($data);
        $ctx_eliminar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_eliminar_activacion'] ?? '');
        $ctx_guardar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_guardar_activacion'] ?? '');

        $oHash = new HashF();
        $oHash->setUrl($url_ajax);
        // Mismo conjunto que el POST del bloque ActividadTipo (_actividad_tipo_body: extendida + selects) + activacion.
        $oHash->setCamposForm('extendida!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!activacion');
        $oHash->setCamposNo('id_tipo_activ!que');
        $oHash->setArrayCamposHidden([
            'id_tipo_activ' => $Qid_tipo_activ,
            'que' => '',
            'ctx_eliminar' => $ctx_eliminar,
            'ctx_guardar' => $ctx_guardar,
        ]);

        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'tipo_txt' => $tipo_txt,
            'activacion' => $Qactivacion,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        ob_start();
        $oView->renderizar('activacion_form.html.twig', $a_campos);
        AjaxJsonSupport::html((string) ob_get_clean());
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

        // Solo para obtener ctx_guardar en modo 'nuevo' (sin id_tipo_activ:
        // el valor real lo compone el propio formulario a partir del selector).
        $dataNuevo = PostRequest::getDataFromUrl('/src/pasarela/tipo_activ_txt_data', ['id_tipo_activ' => '']);
        $ctx_guardar_nuevo = \frontend\shared\helpers\PayloadCoercion::string($dataNuevo['ctx_guardar_activacion'] ?? '');

        $oHash = new HashF();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('extendida!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!activacion');
        $oHash->setCamposNo('id_tipo_activ!que');
        $oHash->setArrayCamposHidden([
            'id_tipo_activ' => '',
            'que' => '',
            'ctx_guardar' => $ctx_guardar_nuevo,
        ]);

        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'oActividadTipo' => $oActividadTipo,
        ];

        $oView = new ViewNewTwig('frontend/pasarela/controller');
        ob_start();
        $oView->renderizar('activacion_form_nuevo.html.twig', $a_campos);
        AjaxJsonSupport::html((string) ob_get_clean());
}
