<?php

use frontend\shared\helpers\PayloadCoercion;
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
use frontend\dossiers\helpers\DossiersListaSupport;
use frontend\dossiers\helpers\DossiersPayload;
use frontend\shared\helpers\ListNavSupport;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
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
        \frontend\shared\helpers\ListNavSupport::olvidarForwardFromDossiersSlot((int) $stackFromPost);
    }
}

$Qrefresh = \frontend\shared\helpers\PayloadCoercion::int($requestPayload['refresh'] ?? 0);
$pararRecordar = \frontend\shared\helpers\ListNavSupport::pararRecordarForDossiersRefresh($Qrefresh);
$gstackFromPost = filter_input(INPUT_POST, 'Gstack', FILTER_VALIDATE_INT);
$segmentChanged = \frontend\shared\helpers\ListNavSupport::dossiersSegmentChangedVsStackTop();
if (is_int($gstackFromPost) && $gstackFromPost > 0) {
    \frontend\shared\helpers\ListNavSupport::bootDossiersFromActividadSelect($oPosicion, $pararRecordar);
    \frontend\shared\helpers\ListNavSupport::persistAsistentesDossierSnapshot($oPosicion);
} elseif ($returningViaStack || \frontend\shared\helpers\ListNavSupport::stackTopIsDossierChildForm()) {
    // Vuelta por pila o recarga tras hijo (js_atras tras guardar cargo): no append con recordar().
} elseif (!\frontend\shared\helpers\ListNavSupport::stackTopIsDossiersVer()) {
    \frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $pararRecordar);
} elseif ($Qrefresh > 0 || $segmentChanged) {
    \frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $pararRecordar);
}

\frontend\shared\helpers\ListNavSupport::purgeDossierChildFormsFromStack();

$shouldRefreshDossiersEntry = ($returningViaStack || \frontend\shared\helpers\ListNavSupport::stackTopIsDossiersVer())
    && !$segmentChanged
    && $Qrefresh <= 0
    && !(is_int($gstackFromPost) && $gstackFromPost > 0);
if ($shouldRefreshDossiersEntry) {
    \frontend\shared\helpers\ListNavSupport::refreshStackEntryOnReturn(
        $oPosicion,
        $returningViaStack ? (int) $stackFromPost : \frontend\shared\helpers\ListNavSupport::findBestDossiersStackKey(),
    );
}

$idDossierEarly = trim(\frontend\shared\helpers\PayloadCoercion::string($requestPayload['id_dossier'] ?? ''));
$claseInfoEarly = trim(\frontend\shared\helpers\PayloadCoercion::string($requestPayload['clase_info'] ?? ''));
if ($Qrefresh > 0) {
    \frontend\shared\helpers\ListNavSupport::persistSelectionOnListPage(
        $oPosicion,
        \frontend\shared\helpers\ListNavSupport::idSelFromPost(),
        \frontend\shared\helpers\ListNavSupport::scrollIdFromPost(),
        false,
    );
} elseif ($returningViaStack && !\frontend\shared\helpers\ListNavSupport::idSelIsEmpty(\frontend\shared\helpers\ListNavSupport::idSelForLista($restoredIdSelFromStack))) {
    \frontend\shared\helpers\ListNavSupport::persistSelectionOnListPage(
        $oPosicion,
        \frontend\shared\helpers\ListNavSupport::idSelForLista($restoredIdSelFromStack),
        is_scalar($restoredScrollIdFromStack) ? (string) $restoredScrollIdFromStack : '',
        false,
    );
} elseif ($idDossierEarly === '' && $claseInfoEarly === '') {
    \frontend\shared\helpers\ListNavSupport::persistSelectionToPosicion($oPosicion, 1);
} elseif (
    (\frontend\shared\helpers\ListNavSupport::selFromPost() !== [] || \frontend\shared\helpers\ListNavSupport::scrollIdFromPost() !== '')
    && is_int($gstackFromPost) && $gstackFromPost > 0
) {
    // Solo desde listado externo (actividad_select, personas_select, …), no dossier → dossier.
    \frontend\shared\helpers\ListNavSupport::persistSelectionToPosicion($oPosicion, 1);
}

// Resolver estado de navegación aquí (frontend) y pasárselo al builder como input plano.
$requestPayload['stack_actual'] = $oPosicion->getStack(0);

$apiPayload = PostRequest::requestPayloadForHash();
$idDossierReq = trim(\frontend\shared\helpers\PayloadCoercion::string($apiPayload['id_dossier'] ?? ''));
$claseInfoReq = trim(\frontend\shared\helpers\PayloadCoercion::string($apiPayload['clase_info'] ?? ''));
if ($idDossierReq === '' && $claseInfoReq === '') {
    foreach (['queSel', 'que', 'mod', 'clase_info', 'bloque', 'permiso', 'depende', 'id_dossier'] as $extraKey) {
        unset($apiPayload[$extraKey]);
    }
    // Solo descartar sel si ya conocemos id_pau (p. ej. volver a la lista desde una ficha).
    // Desde listados externos (personas_select) id_pau no viaja y sel es la fuente del id.
    $idPauReq = \frontend\shared\helpers\PayloadCoercion::int($apiPayload['id_pau'] ?? 0);
    if ($idPauReq > 0) {
        unset($apiPayload['sel']);
    }
}

\frontend\shared\helpers\ListNavSupport::applyRestoredSelectionToApiPayload(
    $apiPayload,
    $restoredIdSelFromStack,
    $restoredScrollIdFromStack,
);

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_ver_pantalla_data', $apiPayload, false);
$errorMsg = \frontend\shared\helpers\PayloadCoercion::string($data['error'] ?? '');
if ($errorMsg !== '') {
    echo PostRequest::stripInternalCallProvenance($errorMsg);
    return;
}

$avisoRegionStgr = \frontend\shared\helpers\PayloadCoercion::string($data['aviso'] ?? '');
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

echo \frontend\shared\helpers\ListNavSupport::mostrarLeftSlideFromDossiers($oPosicion);

$oViewTop = new ViewNewPhtml('frontend\\dossiers\\view');
$oViewTop->renderizar('dossiers_ver_top.phtml', [
    'web_icons' => \frontend\shared\helpers\PayloadCoercion::string($topData['web_icons'] ?? ''),
    'alt_dossiers' => \frontend\shared\helpers\PayloadCoercion::string($topData['alt_dossiers'] ?? ''),
    'txt_dossiers' => \frontend\shared\helpers\PayloadCoercion::string($topData['txt_dossiers'] ?? ''),
    'nom_cabecera' => \frontend\shared\helpers\PayloadCoercion::string($topData['nom_cabecera'] ?? ''),
    'go_dossiers' => $goDossiers,
    'go_home' => $goHome,
]);

if (\frontend\shared\helpers\PayloadCoercion::string($data['modo'] ?? '') === 'lista') {
    $a_filas = DossiersListaSupport::signFilas($data['lista_a_filas'] ?? [], ['href_ver', 'href_abrir']);
    echo "<div id=\"ficha\">";
    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');
    $oView->renderizar('lista_dossiers.phtml', [
        'a_filas' => $a_filas,
        'web_icons' => \frontend\shared\helpers\PayloadCoercion::string($topData['web_icons'] ?? OrbixRuntime::getWebIcons()),
    ]);
    echo "</div>";
} else {
    $segmentos = DossiersPayload::listRows($data['ficha_segmentos'] ?? []);
    foreach ($segmentos as $seg) {
        $id = \frontend\shared\helpers\PayloadCoercion::string($seg['id'] ?? '');
        $tipo = \frontend\shared\helpers\PayloadCoercion::string($seg['tipo'] ?? '');
        echo '<div id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
        if ($tipo === 'html') {
            echo \frontend\shared\helpers\PayloadCoercion::string($seg['html'] ?? '');
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
