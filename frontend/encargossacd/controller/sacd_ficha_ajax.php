<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use web\Hash;

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
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');

switch ($Qque) {
    case 'get_select':
        $Qfiltro_sacd = (string)filter_input(INPUT_POST, 'filtro_sacd');
        $data = PostRequest::getDataFromUrl('/src/encargossacd/sacd_select_data', [
            'filtro_sacd' => $Qfiltro_sacd,
            'id_nom' => $Qid_nom,
        ]);
        $opciones = is_array($data['opciones'] ?? null) ? $data['opciones'] : [];
        $prefix = (string)($data['label_prefix'] ?? '');

        $oDespl = new Desplegable();
        $oDespl->setBlanco(true);
        $oDespl->setOpciones($opciones);
        $oDespl->setOpcion_sel($Qid_nom);
        $oDespl->setNombre('lst_sacds');
        $oDespl->setAction('fnjs_ver_ficha()');

        echo $prefix;
        echo $oDespl->desplegable();
        break;

    case 'ficha':
        $data = PostRequest::getDataFromUrl('/src/encargossacd/sacd_ficha_data', [
            'id_nom' => $Qid_nom,
        ]);

        $permiso = (int)($data['permiso'] ?? 0);
        $observ_sacd = (string)($data['observ_sacd'] ?? '');
        $encargos = is_array($data['encargos'] ?? null) ? $data['encargos'] : [];
        $opciones_mas = is_array($data['opciones_mas'] ?? null) ? $data['opciones_mas'] : [];
        $avisos = is_array($data['avisos'] ?? null) ? $data['avisos'] : [];

        foreach ($encargos as $idx => $e) {
            $aQuery = ['id_ubi' => (int)($e['id_ubi'] ?? 0)];
            array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
            $encargos[$idx]['pagina_ctr'] = Hash::link(
                'frontend/encargossacd/controller/ctr_ficha.php?' . http_build_query($aQuery),
            );
        }

        $oDesplEncs = new Desplegable();
        $oDesplEncs->setNombre('mas');
        $oDesplEncs->setOpciones($opciones_mas);
        $oDesplEncs->setBlanco(1);
        $oDesplEncs->setAction("fnjs_mas_enc();");

        $oHash = new Hash();
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
        PostRequest::getDataFromUrl('/src/encargossacd/sacd_ficha_update', $_POST);
        break;

    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}
