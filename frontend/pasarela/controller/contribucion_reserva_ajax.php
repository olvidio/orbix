<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

/**
 * Dispatcher AJAX para el parámetro `contribucion_reserva`.
 *
 * Misma estructura que `activacion_ajax.php`: cada `que` delega en un endpoint
 * `/src/pasarela/contribucion_reserva_*` vía `PostRequest::getDataFromUrl(...)`
 * y, si toca, monta el HTML/Twig en frontend.
 */

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/contribucion_reserva_ajax.php';

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'eliminar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        PostRequest::getDataFromUrl('/src/pasarela/contribucion_reserva_excepcion_eliminar', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        break;
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qcontribucion = (string)filter_input(INPUT_POST, 'contribucion');
        PostRequest::getDataFromUrl('/src/pasarela/contribucion_reserva_excepcion_guardar', [
            'id_tipo_activ' => $Qid_tipo_activ,
            'valor' => $Qcontribucion,
        ]);
        break;
    case 'update_default':
        $Qdefault = (string)filter_input(INPUT_POST, 'default');
        PostRequest::getDataFromUrl('/src/pasarela/contribucion_reserva_default_guardar', [
            'default' => $Qdefault,
        ]);
        break;
    case 'lista':
        $data = PostRequest::getDataFromUrl('/src/pasarela/contribucion_reserva_lista');
        echo render_contribucion_reserva_lista_html($data);
        break;
    case 'form_default':
        $data = PostRequest::getDataFromUrl('/src/pasarela/contribucion_reserva_default_data');
        $default = (string)($data['default'] ?? '');
        $txt = _('Valor por defecto en €');

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
        $txt = _('Contribución en concepto de reserva');
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qcontribucion = (string)filter_input(INPUT_POST, 'contribucion');

        $data = PostRequest::getDataFromUrl('/src/pasarela/tipo_activ_txt_data', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        $tipo_txt = (string)($data['tipo_txt'] ?? '');

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
        $txt = _('Contribución en concepto de reserva');
        $Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
        $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
        $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

        $oActividadTipo = new \src\actividades\application\ActividadTipo();
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

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        $oView->renderizar('contribucion_x_form_nuevo.html.twig', $a_campos);
        break;
}

/**
 * Renderiza el HTML del listado de contribución_reserva para `#div_tabla`.
 *
 * @param array{default?: string, excepciones?: array<int, array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
 */
function render_contribucion_reserva_lista_html(array $data): string
{
    $default = htmlspecialchars((string)($data['default'] ?? ''), ENT_QUOTES, 'UTF-8');
    $excepciones = $data['excepciones'] ?? [];

    $html = '<table>';
    $html .= '<tr><td>' . _('por defecto') . '</td><td>';
    $html .= '<span class="link" onclick="fnjs_modificar_default()">' . $default . '</span></td></tr>';
    $html .= '</table><table>';
    foreach ($excepciones as $row) {
        $id_tipo_activ = (int)($row['id_tipo_activ'] ?? 0);
        $etiqueta = htmlspecialchars((string)($row['etiqueta'] ?? ''), ENT_QUOTES, 'UTF-8');
        $valor = (string)($row['valor'] ?? '');
        $valor_js = htmlspecialchars(addslashes($valor), ENT_QUOTES, 'UTF-8');
        $valor_html = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        $html .= "<tr><td>$etiqueta</td><td>";
        $html .= "<span class=\"link\" onclick=\"fnjs_modificar($id_tipo_activ,'$valor_js')\">$valor_html</span></td></tr>";
    }
    $html .= '</table>';
    return $html;
}
