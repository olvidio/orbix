<?php

/**
 * HTML de la tabla de relación de dossiers.
 * Llamar desde controladores con {@see orbix_render_lista_dossiers()}.
 */
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFrontSignedLink;

function orbix_render_lista_dossiers(string $pau, int $id_pau, string $Qobj_pau): string
{
    $data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_lista_fichas_data', [
        'pau' => $pau,
        'id_pau' => $id_pau,
        'obj_pau' => $Qobj_pau,
    ], false);

    if (!empty($data['error'])) {
        return '<div class="certificado-aviso-config" role="alert" style="max-width: 42rem; padding: 1rem 1.25rem; margin: 1rem 0; border: 1px solid #c9a227; background: #fffbea; color: #3d3500;">'
            . PostRequest::stripInternalCallProvenance((string) $data['error'])
            . '</div>';
    }

    $data['a_filas'] = HashFrontSignedLink::signRowLinkSpecs(
        (array)($data['a_filas'] ?? []),
        ['href_ver', 'href_abrir']
    );
    $data['web_icons'] = (string)($data['web_icons'] ?? OrbixRuntime::getWebIcons());

    $oView = new ViewNewPhtml('frontend\\dossiers\\controller');

    return $oView->renderizar('lista_dossiers.phtml', $data, false);
}
