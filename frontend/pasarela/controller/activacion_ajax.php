<?php

use frontend\actividades\helpers\ActividadTipo;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

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
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        PostRequest::getDataFromUrl('/src/pasarela/activacion_excepcion_eliminar', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => true], JSON_THROW_ON_ERROR);
        break;
    case 'update':
    case 'nuevo':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qactivacion = (string)filter_input(INPUT_POST, 'activacion');
        PostRequest::getDataFromUrl('/src/pasarela/activacion_excepcion_guardar', [
            'id_tipo_activ' => $Qid_tipo_activ,
            'valor' => $Qactivacion,
        ]);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => true], JSON_THROW_ON_ERROR);
        break;
    case 'update_default':
        $Qdefault = (string)filter_input(INPUT_POST, 'default');
        PostRequest::getDataFromUrl('/src/pasarela/activacion_default_guardar', [
            'default' => $Qdefault,
        ]);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => true], JSON_THROW_ON_ERROR);
        break;
    case 'lista':
        $data = PostRequest::getDataFromUrl('/src/pasarela/activacion_lista');
        echo render_activacion_lista_html($data);
        break;
    case 'form_default':
        $data = PostRequest::getDataFromUrl('/src/pasarela/activacion_default_data');
        $default = (string)($data['default'] ?? '');

        $oHash = new HashFront();
        $oHash->setUrl($url_ajax);
        $oHash->setCamposForm('default');
        $oHash->setArrayCamposHidden(['que' => 'update_default']);
        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url_ajax' => $url_ajax,
            'default' => $default,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        $oView->renderizar('activacion_default_form.html.twig', $a_campos);
        break;
    case 'form_modificar':
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qactivacion = (string)filter_input(INPUT_POST, 'activacion');

        $data = PostRequest::getDataFromUrl('/src/pasarela/tipo_activ_txt_data', [
            'id_tipo_activ' => $Qid_tipo_activ,
        ]);
        $tipo_txt = (string)($data['tipo_txt'] ?? '');

        $oHash = new HashFront();
        $oHash->setUrl($url_ajax);
        // Mismo conjunto que el POST del bloque ActividadTipo (_actividad_tipo_body: extendida + selects) + activacion.
        $oHash->setCamposForm('extendida!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!activacion');
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
            'activacion' => $Qactivacion,
        ];

        $oView = new ViewNewTwig('frontend\\pasarela\\controller');
        $oView->renderizar('activacion_form.html.twig', $a_campos);
        break;
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
        $oHash->setCamposForm('extendida!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!activacion');
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

        $oView = new ViewNewTwig('frontend/pasarela/controller');
        $oView->renderizar('activacion_form_nuevo.html.twig', $a_campos);
        break;
}

/**
 * Renderiza el HTML de la tabla de activaciones consumido por `#div_tabla`.
 *
 * @param array{default?: string, excepciones?: array<int, array{id_tipo_activ: string, etiqueta: string, valor: string}>} $data
 */
function render_activacion_lista_html(array $data): string
{
    $default = htmlspecialchars((string)($data['default'] ?? ''), ENT_QUOTES, 'UTF-8');
    $excepciones = $data['excepciones'] ?? [];

    $html = '<table>';
    $html .= '<tr><td>' . _('por defecto') . '</td><td>';
    $html .= '<span class="link" onclick="fnjs_modificar_activacion_default()">' . $default . '</span></td></tr>';
    $html .= '</table><table>';
    foreach ($excepciones as $row) {
        $id_tipo_activ = (int)($row['id_tipo_activ'] ?? 0);
        $etiqueta = htmlspecialchars((string)($row['etiqueta'] ?? ''), ENT_QUOTES, 'UTF-8');
        $valor = (string)($row['valor'] ?? '');
        $valor_js = htmlspecialchars(addslashes($valor), ENT_QUOTES, 'UTF-8');
        $valor_html = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        $html .= "<tr><td>$etiqueta</td><td>";
        $html .= "<span class=\"link\" onclick=\"fnjs_modificar_activacion($id_tipo_activ,'$valor_js')\">$valor_html</span></td></tr>";
    }
    $html .= '</table>';
    return $html;
}
