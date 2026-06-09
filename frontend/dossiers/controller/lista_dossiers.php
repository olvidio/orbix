<?php

/**
 * HTML de la tabla de relación de dossiers.
 * Llamar desde controladores con {@see orbix_render_lista_dossiers()}.
 */
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

function orbix_render_lista_dossiers(string $pau, int $id_pau, string $Qobj_pau): string
{
    require_once 'frontend/dossiers/helpers/dossiers_support.php';

    $data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_lista_fichas_data', [
        'pau' => $pau,
        'id_pau' => $id_pau,
        'obj_pau' => $Qobj_pau,
    ], false);

    $error = tessera_imprimir_string($data['error'] ?? '');
    if ($error !== '') {
        return '<div class="certificado-aviso-config" role="alert" style="max-width: 42rem; padding: 1rem 1.25rem; margin: 1rem 0; border: 1px solid #c9a227; background: #fffbea; color: #3d3500;">'
            . PostRequest::stripInternalCallProvenance($error)
            . '</div>';
    }

    $viewData = dossiers_view_variables($data);
    $viewData['a_filas'] = dossiers_sign_lista_filas($data['a_filas'] ?? [], ['href_ver', 'href_abrir']);
    $viewData['web_icons'] = tessera_imprimir_string($data['web_icons'] ?? OrbixRuntime::getWebIcons());

    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');

    return $oView->renderizar('lista_dossiers.phtml', $viewData, false);
}
