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

$navState = ListNavSupport::buildDossiersVerStackParametros();

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ListNavSupport::buildDossiersVerNavIdentity($navState),
    $navState,
);

ListNavSupport::syncActividadSelectParentSelection($oPosicion);

$requestPayload = PostRequest::requestPayloadForHash();
$requestPayload['stack_actual'] = 0;

$apiPayload = PostRequest::requestPayloadForHash();
$idDossierReq = trim(PayloadCoercion::string($apiPayload['id_dossier'] ?? ''));
$claseInfoReq = trim(PayloadCoercion::string($apiPayload['clase_info'] ?? ''));
if ($idDossierReq === '' && $claseInfoReq === '') {
    foreach (['queSel', 'que', 'mod', 'clase_info', 'bloque', 'permiso', 'depende', 'id_dossier'] as $extraKey) {
        unset($apiPayload[$extraKey]);
    }
    $idPauReq = PayloadCoercion::int($apiPayload['id_pau'] ?? 0);
    if ($idPauReq > 0) {
        unset($apiPayload['sel']);
    }
}

ListNavSupport::applyRestoredSelectionToApiPayload($apiPayload, null, null);

$data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_ver_pantalla_data', $apiPayload, false);
$errorMsg = PayloadCoercion::string($data['error'] ?? '');
if ($errorMsg !== '') {
    echo PostRequest::stripInternalCallProvenance($errorMsg);
    return;
}

$avisoRegionStgr = PayloadCoercion::string($data['aviso'] ?? '');
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

echo $oPosicion->mostrarNavAtrasFromDossiers();

$oViewTop = new ViewNewPhtml('frontend\\dossiers\\view');
$oViewTop->renderizar('dossiers_ver_top.phtml', [
    'web_icons' => PayloadCoercion::string($topData['web_icons'] ?? ''),
    'alt_dossiers' => PayloadCoercion::string($topData['alt_dossiers'] ?? ''),
    'txt_dossiers' => PayloadCoercion::string($topData['txt_dossiers'] ?? ''),
    'nom_cabecera' => PayloadCoercion::string($topData['nom_cabecera'] ?? ''),
    'go_dossiers' => $goDossiers,
    'go_home' => $goHome,
]);

if (PayloadCoercion::string($data['modo'] ?? '') === 'lista') {
    $a_filas = DossiersListaSupport::signFilas($data['lista_a_filas'] ?? [], ['href_ver', 'href_abrir']);
    echo "<div id=\"ficha\">";
    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');
    $oView->renderizar('lista_dossiers.phtml', [
        'a_filas' => $a_filas,
        'web_icons' => PayloadCoercion::string($topData['web_icons'] ?? OrbixRuntime::getWebIcons()),
    ]);
    echo "</div>";
} else {
    $segmentos = DossiersPayload::listRows($data['ficha_segmentos'] ?? []);
    foreach ($segmentos as $seg) {
        $id = PayloadCoercion::string($seg['id'] ?? '');
        $tipo = PayloadCoercion::string($seg['tipo'] ?? '');
        echo '<div id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">';
        if ($tipo === 'html') {
            echo PayloadCoercion::string($seg['html'] ?? '');
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
