<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

require_once __DIR__ . '/ubiscamas_support.php';

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
        $oHashSelect->setCamposForm(tessera_imprimir_string($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo(tessera_imprimir_string($hash['campos_no'] ?? ''));
        $oHashSelect->setArrayCamposHidden(ubiscamas_hash_campos_hidden($hash['campos_hidden'] ?? []));

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(tessera_imprimir_string($tabla['id_tabla'] ?? 'select2006'));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $signed = SelectHabitacionesCdcUrlSigning::sign(
            ubiscamas_cdc_url_signing_input(
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
