<?php

use frontend\shared\config\OrbixRuntime;
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
use frontend\shared\FrontBootstrap;
use frontend\shared\web\Posicion;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/dossiers/helpers/dossiers_support.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$requestPayload = PostRequest::requestPayloadForHash();
$stackFromPost = isset($requestPayload['stack']) ? (string) filter_var($requestPayload['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
$returningViaStack = $stackFromPost !== '' && $stackFromPost !== '0';

// Leer selección ANTES de recordar(): recordar() puede reindexar la pila y el stack del POST deja de coincidir.
$restoredIdSelFromStack = null;
$restoredScrollIdFromStack = null;
if ($returningViaStack) {
    $oPosicionRestore = new Posicion();
    if ($oPosicionRestore->goStack((int) $stackFromPost)) {
        $restoredIdSelFromStack = $oPosicionRestore->getParametro('id_sel');
        $restoredScrollIdFromStack = $oPosicionRestore->getParametro('scroll_id');
        $oPosicionRestore->olvidar((int) $stackFromPost);
    } else {
        list_nav_olvidar_forward_from_dossiers_slot((int) $stackFromPost);
    }
}

$Qrefresh = tessera_imprimir_int($requestPayload['refresh'] ?? 0);
$pararRecordar = list_nav_parar_recordar_for_dossiers_refresh($Qrefresh);
$gstackFromPost = filter_input(INPUT_POST, 'Gstack', FILTER_VALIDATE_INT);
if (is_int($gstackFromPost) && $gstackFromPost > 0) {
    list_nav_boot_dossiers_from_actividad_select($oPosicion, $pararRecordar);
} elseif ($returningViaStack || list_nav_stack_top_is_dossier_child_form()) {
    // Vuelta por pila o recarga tras hijo (js_atras tras guardar cargo): no append con recordar().
} elseif (!list_nav_stack_top_is_dossiers_ver() || $Qrefresh > 0) {
    list_nav_boot_recordar($oPosicion, $pararRecordar);
}

list_nav_purge_dossier_child_forms_from_stack();

if ($returningViaStack || list_nav_stack_top_is_dossiers_ver()) {
    list_nav_refresh_stack_entry_on_return(
        $oPosicion,
        $returningViaStack ? (int) $stackFromPost : list_nav_find_best_dossiers_stack_key(),
    );
}

$idDossierEarly = trim(tessera_imprimir_string($requestPayload['id_dossier'] ?? ''));
$claseInfoEarly = trim(tessera_imprimir_string($requestPayload['clase_info'] ?? ''));
if ($Qrefresh > 0) {
    list_nav_persist_selection_on_list_page(
        $oPosicion,
        list_nav_id_sel_from_post(),
        list_nav_scroll_id_from_post(),
        false,
    );
} elseif ($returningViaStack && !list_nav_id_sel_is_empty(list_nav_id_sel_for_lista($restoredIdSelFromStack))) {
    list_nav_persist_selection_on_list_page(
        $oPosicion,
        list_nav_id_sel_for_lista($restoredIdSelFromStack),
        is_scalar($restoredScrollIdFromStack) ? (string) $restoredScrollIdFromStack : '',
        false,
    );
} elseif ($idDossierEarly === '' && $claseInfoEarly === '') {
    list_nav_persist_selection_to_posicion($oPosicion, 1);
} elseif (
    (list_nav_sel_from_post() !== [] || list_nav_scroll_id_from_post() !== '')
    && is_int($gstackFromPost) && $gstackFromPost > 0
) {
    // Solo desde listado externo (actividad_select, personas_select, …), no dossier → dossier.
    list_nav_persist_selection_to_posicion($oPosicion, 1);
}

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$requestPayload['stack_actual'] = $oPosicion->getStack(0);

$apiPayload = PostRequest::requestPayloadForHash();
$idDossierReq = trim(tessera_imprimir_string($apiPayload['id_dossier'] ?? ''));
$claseInfoReq = trim(tessera_imprimir_string($apiPayload['clase_info'] ?? ''));
if ($idDossierReq === '' && $claseInfoReq === '') {
    foreach (['queSel', 'que', 'mod', 'clase_info', 'bloque', 'permiso', 'depende', 'id_dossier'] as $extraKey) {
        unset($apiPayload[$extraKey]);
    }
    // Solo descartar sel si ya conocemos id_pau (p. ej. volver a la lista desde una ficha).
    // Desde listados externos (personas_select) id_pau no viaja y sel es la fuente del id.
    $idPauReq = tessera_imprimir_int($apiPayload['id_pau'] ?? 0);
    if ($idPauReq > 0) {
        unset($apiPayload['sel']);
    }
}

list_nav_apply_restored_selection_to_api_payload(
    $apiPayload,
    $restoredIdSelFromStack,
    $restoredScrollIdFromStack,
);

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_ver_pantalla_data', $apiPayload, false);
$errorMsg = tessera_imprimir_string($data['error'] ?? '');
if ($errorMsg !== '') {
    echo PostRequest::stripInternalCallProvenance($errorMsg);
    return;
}

$avisoRegionStgr = tessera_imprimir_string($data['aviso'] ?? '');
if ($avisoRegionStgr !== '') {
    echo '<div class="certificado-aviso-config" role="alert" style="max-width: 42rem; padding: 1rem 1.25rem; margin: 1rem 0; border: 1px solid #c9a227; background: #fffbea; color: #3d3500; line-height: 1.5;">';
    echo '<div style="margin: 0;">' . $avisoRegionStgr . '</div>';
    echo '</div>';
}

// ----- Firma de link_specs en el frontend (HashFront vive sólo en frontend/) -----
$topData = $data['top_data'] ?? [];
if (!is_array($topData)) {
    $topData = [];
}
$goDossiers = HashFrontSignedLink::tryFromSpec($topData['go_dossiers_link_spec'] ?? null);
$goHome = HashFrontSignedLink::tryFromSpec($topData['go_home_link_spec'] ?? null);

echo list_nav_mostrar_left_slide_to_list_parent_from_dossiers($oPosicion);

$oViewTop = new ViewNewPhtml('frontend\\dossiers\\view');
$oViewTop->renderizar('dossiers_ver_top.phtml', [
    'web_icons' => tessera_imprimir_string($topData['web_icons'] ?? ''),
    'alt_dossiers' => tessera_imprimir_string($topData['alt_dossiers'] ?? ''),
    'txt_dossiers' => tessera_imprimir_string($topData['txt_dossiers'] ?? ''),
    'nom_cabecera' => tessera_imprimir_string($topData['nom_cabecera'] ?? ''),
    'go_dossiers' => $goDossiers,
    'go_home' => $goHome,
]);

if (tessera_imprimir_string($data['modo'] ?? '') === 'lista') {
    $a_filas = dossiers_sign_lista_filas($data['lista_a_filas'] ?? [], ['href_ver', 'href_abrir']);
    echo "<div id=\"ficha\">";
    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');
    $oView->renderizar('lista_dossiers.phtml', [
        'a_filas' => $a_filas,
        'web_icons' => tessera_imprimir_string($topData['web_icons'] ?? OrbixRuntime::getWebIcons()),
    ]);
    echo "</div>";
} else {
    $segmentos = dossiers_list_rows($data['ficha_segmentos'] ?? []);
    foreach ($segmentos as $seg) {
        $id = tessera_imprimir_string($seg['id'] ?? '');
        $tipo = tessera_imprimir_string($seg['tipo'] ?? '');
        echo '<div id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
        if ($tipo === 'html') {
            echo tessera_imprimir_string($seg['html'] ?? '');
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
