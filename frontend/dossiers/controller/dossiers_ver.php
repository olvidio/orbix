<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFrontSignedLink;
use frontend\dossiers\helpers\DossiersVerFichaDatosTabla;
use frontend\ubiscamas\helpers\SelectHabitacionesCdcRender;
use frontend\actividadestudios\helpers\SelectAsignaturasDeUnaActividadRender;
use frontend\asistentes\helpers\SelectActividadesDeUnaPersonaRender;
use frontend\asistentes\helpers\SelectAsistentesAUnaActividadRender;
use frontend\certificados\helpers\SelectCertificadosDeUnaPersonaRender;
use frontend\notas\helpers\SelectNotasDeUnaPersonaRender;
use frontend\actividadestudios\helpers\SelectMatriculasDeUnaActividadRender;
use frontend\actividadestudios\helpers\SelectMatriculasDeUnaPersonaRender;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$requestPayload = PostRequest::requestPayloadForHash();
$Qrefresh = (int)($requestPayload['refresh'] ?? 0);
$oPosicion->recordar($Qrefresh);

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$requestPayload['stack_actual'] = $oPosicion->getStack(0);

$stackFromPost = isset($requestPayload['stack']) ? (string) filter_var($requestPayload['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $requestPayload['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $requestPayload['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_ver_pantalla_data', $requestPayload);
if (!is_array($data)) {
    exit;
}

// ----- Firma de link_specs en el frontend (HashFront vive sólo en frontend/) -----
$topData = (array)($data['top_data'] ?? []);
$goDossiers = isset($topData['go_dossiers_link_spec']) && is_array($topData['go_dossiers_link_spec'])
    ? HashFrontSignedLink::fromSpec($topData['go_dossiers_link_spec'])
    : '';
$goHome = isset($topData['go_home_link_spec']) && is_array($topData['go_home_link_spec'])
    ? HashFrontSignedLink::fromSpec($topData['go_home_link_spec'])
    : '';

echo $oPosicion->mostrar_left_slide(1);

$oViewTop = new ViewNewPhtml('frontend\\dossiers\\view');
$oViewTop->renderizar('dossiers_ver_top.phtml', [
    'web_icons' => (string) ($topData['web_icons'] ?? ''),
    'alt_dossiers' => (string) ($topData['alt_dossiers'] ?? ''),
    'txt_dossiers' => (string) ($topData['txt_dossiers'] ?? ''),
    'nom_cabecera' => (string) ($topData['nom_cabecera'] ?? ''),
    'go_dossiers' => $goDossiers,
    'go_home' => $goHome,
]);

if (($data['modo'] ?? '') === 'lista') {
    $a_filas = HashFrontSignedLink::signRowLinkSpecs(
        (array)($data['lista_a_filas'] ?? []),
        ['href_ver', 'href_abrir']
    );
    echo "<div id=\"ficha\">";
    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');
    $oView->renderizar('lista_dossiers.phtml', [
        'a_filas' => $a_filas,
        'web_icons' => (string) ($topData['web_icons'] ?? ''),
    ]);
    echo "</div>";
} else {
    $segmentos = (array) ($data['ficha_segmentos'] ?? []);
    foreach ($segmentos as $seg) {
        if (!is_array($seg)) {
            continue;
        }
        $id = (string) ($seg['id'] ?? '');
        $tipo = (string) ($seg['tipo'] ?? '');
        echo '<div id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
        if ($tipo === 'html') {
            echo (string) ($seg['html'] ?? '');
        } elseif ($tipo === 'select_habitaciones_cdc') {
            echo SelectHabitacionesCdcRender::render($seg);
        } elseif ($tipo === 'select_asignaturas_de_una_actividad') {
            echo SelectAsignaturasDeUnaActividadRender::render($seg);
        } elseif ($tipo === 'select_matriculas_de_una_persona') {
            echo SelectMatriculasDeUnaPersonaRender::render($seg);
        } elseif ($tipo === 'select_matriculas_de_una_actividad') {
            echo SelectMatriculasDeUnaActividadRender::render($seg);
        } elseif ($tipo === 'select_actividades_de_una_persona') {
            echo SelectActividadesDeUnaPersonaRender::render($seg);
        } elseif ($tipo === 'select_asistentes_a_una_actividad') {
            echo SelectAsistentesAUnaActividadRender::render($seg);
        } elseif ($tipo === 'select_certificados_de_una_persona') {
            echo SelectCertificadosDeUnaPersonaRender::render($seg);
        } elseif ($tipo === 'select_notas_de_una_persona') {
            echo SelectNotasDeUnaPersonaRender::render($seg);
        } elseif ($tipo === 'datos_tabla') {
            echo DossiersVerFichaDatosTabla::render($seg);
        }
        echo '</div>';
    }
}
