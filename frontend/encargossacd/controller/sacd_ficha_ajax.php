<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\encargossacd\helpers\EncargossacdPostInput;
use frontend\encargossacd\helpers\EncargossacdPayload;

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * AJAX de la ficha SACD. Despacha entre las acciones del lado frontend
 * (`get_select`, `ficha`, `update`) delegando lectura/mutacion a los
 * endpoints backend `/src/encargossacd/sacd_*`.
 *
 * - `get_select` -> lista de SACDs filtrados (HTML).
 * - `ficha`       -> ficha de encargos del SACD (HTML, vista
 *   `sacd_ficha_ajax_ficha.phtml`).
 * - `update`      -> proxy JSON->texto plano para compat. con `alert`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = EncargossacdPostInput::postString('que');
$Qid_nom = EncargossacdPostInput::postInt('id_nom');

switch ($Qque) {
    case 'get_select':
        $Qfiltro_sacd = EncargossacdPostInput::postString('filtro_sacd');
        $data = PostRequest::getDataFromUrl('/src/encargossacd/sacd_select_data', [
            'filtro_sacd' => $Qfiltro_sacd,
            'id_nom' => $Qid_nom,
        ]);
        $prefix = \frontend\shared\helpers\PayloadCoercion::string($data['label_prefix'] ?? '');

        $oDespl = new Desplegable();
        $oDespl->setBlanco(true);
        $oDespl->setOpciones(EncargossacdPayload::desplegableOpciones($data['opciones'] ?? []));
        $oDespl->setOpcion_sel(EncargossacdPayload::desplegableOpcionSel($Qid_nom));
        $oDespl->setNombre('lst_sacds');
        $oDespl->setAction('fnjs_ver_ficha()');

        AjaxJsonSupport::html($prefix . $oDespl->desplegable());

    case 'ficha':
        $data = PostRequest::getDataFromUrl('/src/encargossacd/sacd_ficha_data', [
            'id_nom' => $Qid_nom,
        ]);

        $permiso = \frontend\shared\helpers\PayloadCoercion::int($data['permiso'] ?? 0);
        $observ_sacd = \frontend\shared\helpers\PayloadCoercion::string($data['observ_sacd'] ?? '');
        $encargos = EncargossacdPayload::sacdFichaEncargosFromPayload($data['encargos'] ?? null);
        $opciones_mas = EncargossacdPayload::desplegableOpciones($data['opciones_mas'] ?? []);
        $avisos = is_array($data['avisos'] ?? null) ? $data['avisos'] : [];

        foreach ($encargos as $idx => $e) {
            $aQuery = ['id_ubi' => EncargossacdPayload::sacdFichaEncargoIdUbi($e)];
            array_walk($aQuery, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
            $encargos[$idx]['pagina_ctr'] = HashFront::link(
                'frontend/encargossacd/controller/ctr_ficha.php?' . http_build_query($aQuery),
            );
        }

        $oDesplEncs = new Desplegable();
        $oDesplEncs->setNombre('mas');
        $oDesplEncs->setOpciones($opciones_mas);
        $oDesplEncs->setBlanco(EncargossacdPayload::desplegableBlanco(1));
        $oDesplEncs->setAction("fnjs_mas_enc();");

        $oHash = new HashFront();
        $oHash->setCamposForm('enc_num!mas!observ!dedic_m!dedic_t!dedic_v!id_tipo_enc');
        $oHash->setcamposNo('id_enc!mas!refresh');
        $oHash->setArrayCamposHidden([
            'que' => 'update',
            'id_nom' => $Qid_nom,
        ]);

        $a_campos = [
            'permiso' => $permiso,
            'observ_sacd' => $observ_sacd,
            'encargos' => $encargos,
            'avisos' => $avisos,
            'oDesplEncs' => $oDesplEncs,
            'oHash' => $oHash,
            'enc_num' => count($encargos),
            'id_nom' => $Qid_nom,
        ];

        AjaxJsonSupport::renderPhtml('frontend\\encargossacd\\controller', 'sacd_ficha_ajax_ficha.phtml', $a_campos);

    case 'update':
        PostRequest::getDataFromUrl('/src/encargossacd/sacd_ficha_update', PostRequest::requestPayloadForHash());
        AjaxJsonSupport::response();

    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}
