<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
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

$Qque = encargossacd_post_string('que');
$Qid_nom = encargossacd_post_int('id_nom');

switch ($Qque) {
    case 'get_select':
        $Qfiltro_sacd = encargossacd_post_string('filtro_sacd');
        $data = PostRequest::getDataFromUrl('/src/encargossacd/sacd_select_data', [
            'filtro_sacd' => $Qfiltro_sacd,
            'id_nom' => $Qid_nom,
        ]);
        $prefix = tessera_imprimir_string($data['label_prefix'] ?? '');

        $oDespl = new Desplegable();
        $oDespl->setBlanco(true);
        $oDespl->setOpciones(encargossacd_desplegable_opciones($data['opciones'] ?? []));
        $oDespl->setOpcion_sel(encargossacd_desplegable_opcion_sel($Qid_nom));
        $oDespl->setNombre('lst_sacds');
        $oDespl->setAction('fnjs_ver_ficha()');

        echo $prefix;
        echo $oDespl->desplegable();
        break;

    case 'ficha':
        $data = PostRequest::getDataFromUrl('/src/encargossacd/sacd_ficha_data', [
            'id_nom' => $Qid_nom,
        ]);

        $permiso = tessera_imprimir_int($data['permiso'] ?? 0);
        $observ_sacd = tessera_imprimir_string($data['observ_sacd'] ?? '');
        $encargos = encargossacd_sacd_ficha_encargos_from_payload($data['encargos'] ?? null);
        $opciones_mas = encargossacd_desplegable_opciones($data['opciones_mas'] ?? []);
        $avisos = is_array($data['avisos'] ?? null) ? $data['avisos'] : [];

        foreach ($encargos as $idx => $e) {
            $aQuery = ['id_ubi' => encargossacd_sacd_ficha_encargo_id_ubi($e)];
            array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
            $encargos[$idx]['pagina_ctr'] = HashFront::link(
                'frontend/encargossacd/controller/ctr_ficha.php?' . http_build_query($aQuery),
            );
        }

        $oDesplEncs = new Desplegable();
        $oDesplEncs->setNombre('mas');
        $oDesplEncs->setOpciones($opciones_mas);
        $oDesplEncs->setBlanco(encargossacd_desplegable_blanco(1));
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

        $oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
        $oView->renderizar('sacd_ficha_ajax_ficha.phtml', $a_campos);
        break;

    case 'update':
        PostRequest::getDataFromUrl('/src/encargossacd/sacd_ficha_update', PostRequest::requestPayloadForHash());
        break;

    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}
