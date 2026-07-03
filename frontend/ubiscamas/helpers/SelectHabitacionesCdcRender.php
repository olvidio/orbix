<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Compone el bloque dossier 3102 (habitaciones CDC) en frontend: HashFront + URLs firmadas.
 *
 * @see \src\ubiscamas\domain\Select_habitaciones_cdc::getSegmentData()
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
        $oHashSelect->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_no'] ?? ''));
        $oHashSelect->setArrayCamposHidden(UbiscamasPayload::hashCamposHidden($hash['campos_hidden'] ?? []));

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(\frontend\shared\helpers\PayloadCoercion::string($tabla['id_tabla'] ?? 'select2006'));
        $oTabla->setCabeceras(ActividadesListaSupport::cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(ActividadesListaSupport::botones($tabla['botones'] ?? []));
        $oTabla->setDatos(ActividadesListaSupport::datos($tabla['valores'] ?? []));

        $signed = SelectHabitacionesCdcUrlSigning::sign(
            UbiscamasPayload::cdcUrlSigningInput(
                $seg['url_nuevo_spec'] ?? null,
                $seg['a_links_dl_specs'] ?? []
            )
        );

        $oView = new ViewNewPhtml('frontend\ubiscamas\view');

        return $oView->renderizar('select_habitaciones_cdc.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'url_nuevo' => $signed['url_nuevo'],
        ], false);
    }
}
