<?php

declare(strict_types=1);

namespace frontend\dossiers\helpers;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

/**
 * HTML de la tabla de relación de dossiers (vista `lista_dossiers.phtml`).
 */
final class DossiersListaRender
{
    public static function render(string $pau, int $idPau, string $objPau): string
    {
        $data = PostRequest::getDataFromUrl('/src/dossiers/dossiers_lista_fichas_data', [
            'pau' => $pau,
            'id_pau' => $idPau,
            'obj_pau' => $objPau,
        ], false);

        $error = PayloadCoercion::string($data['error'] ?? '');
        if ($error !== '') {
            return '<div class="certificado-aviso-config" role="alert" style="max-width: 42rem; padding: 1rem 1.25rem; margin: 1rem 0; border: 1px solid #c9a227; background: #fffbea; color: #3d3500;">'
                . PostRequest::stripInternalCallProvenance($error)
                . '</div>';
        }

        $viewData = DossiersPayload::viewVariables($data);
        $viewData['a_filas'] = DossiersListaSupport::signFilas($data['a_filas'] ?? [], ['href_ver', 'href_abrir']);
        $viewData['web_icons'] = PayloadCoercion::string($data['web_icons'] ?? OrbixRuntime::getWebIcons());

        $oView = new ViewNewPhtml('frontend\\dossiers\\controller');

        return $oView->renderizar('lista_dossiers.phtml', $viewData, false);
    }
}
