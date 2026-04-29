<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Compone el bloque dossier 3102 (habitaciones CDC) en frontend: HashFront + URLs firmadas.
 *
 * @see \src\ubiscamas\domain\SelectHabitacionesCdc::getSegmentData()
 */
final class SelectHabitacionesCdcRender
{
    /**
     * @param array<string, mixed> $seg payload de ficha_segmentos (incl. tipo/id sobran; se ignoran)
     */
    public static function render(array $seg): string
    {
        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm((string)($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo((string)($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'select2006'));
        $cabeceras = $tabla['cabeceras'] ?? [];
        $botones = $tabla['botones'] ?? [];
        $valores = $tabla['valores'] ?? [];
        $oTabla->setCabeceras(is_array($cabeceras) ? $cabeceras : []);
        $oTabla->setBotones(is_array($botones) ? $botones : []);
        $oTabla->setDatos(is_array($valores) ? $valores : []);

        $urlNuevoSpec = $seg['url_nuevo_spec'] ?? null;
        $aLinksDlSpecs = $seg['a_links_dl_specs'] ?? [];
        if (!is_array($urlNuevoSpec)) {
            $urlNuevoSpec = [];
        }
        if (!is_array($aLinksDlSpecs)) {
            $aLinksDlSpecs = [];
        }

        $signed = SelectHabitacionesCdcUrlSigning::sign([
            'url_nuevo_spec' => $urlNuevoSpec,
            'a_links_dl_specs' => $aLinksDlSpecs,
        ]);

        $oView = new ViewNewPhtml('frontend\ubiscamas\view');

        return $oView->renderizar('select_habitaciones_cdc.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'url_nuevo' => $signed['url_nuevo'],
        ], false);
    }
}
