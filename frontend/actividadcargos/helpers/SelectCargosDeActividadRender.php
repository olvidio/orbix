<?php

declare(strict_types=1);

namespace frontend\actividadcargos\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\helpers\ListaDesdeDatos;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;

/**
 * Bloque del dossier 3102 (cargos de una actividad): firma, Lista y enlaces de alta firmados.
 *
 * @see \src\actividadcargos\application\Select_cargos_de_actividad::getSegmentData()
 */
final class SelectCargosDeActividadRender
{
    /**
     * @param array<string, mixed> $seg segmento de `ficha_segmentos`
     */
    public static function render(array $seg): string
    {
        $hash = is_array($seg['hash'] ?? null) ? $seg['hash'] : [];
        $hidden = is_array($hash['campos_hidden'] ?? null) ? $hash['campos_hidden'] : [];
        $tabla = is_array($seg['tabla'] ?? null) ? $seg['tabla'] : [];
        $links = is_array($seg['links_dl'] ?? null) ? $seg['links_dl'] : [];

        return (new ViewNewPhtml('frontend\\actividadcargos\\controller'))->renderizar('select_cargos_de_actividad.phtml', [
            'oTabla' => ListaDesdeDatos::lista($tabla),
            'hash_select_html' => FormCargosDeActividadHashCompose::selectListaHiddenHtml([
                'campos_no' => PayloadCoercion::string($hash['campos_no'] ?? ''),
                'campos_hidden' => $hidden,
            ]),
            'aLinks_dl' => DossierTipoFormLinkSpecsSigning::signLinkMap($links),
            'txt_eliminar' => PayloadCoercion::string($seg['txt_eliminar'] ?? ''),
            'bloque' => PayloadCoercion::string($seg['bloque'] ?? ''),
            'url_form' => PayloadCoercion::string($seg['url_form'] ?? ''),
            'url_cargo_eliminar' => PayloadCoercion::string($seg['url_cargo_eliminar'] ?? ''),
            'id_sel_value' => PayloadCoercion::string($seg['id_sel_value'] ?? ''),
            'msg_err' => PayloadCoercion::string($seg['msg_err'] ?? ''),
        ], false);
    }
}
