<?php

declare(strict_types=1);

namespace frontend\actividadcargos\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\helpers\ListaDesdeDatos;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\BotonesCurso;

/**
 * Bloque del dossier 1302 (cargos de una persona en actividades): firma, botones de curso,
 * Lista y enlaces de alta firmados.
 *
 * @see \src\actividadcargos\application\Select_cargos_personas_en_actividad::getSegmentData()
 */
final class SelectCargosPersonasEnActividadRender
{
    /**
     * @param array<string, mixed> $seg segmento de `ficha_segmentos`
     */
    public static function render(array $seg): string
    {
        $hash = is_array($seg['hash'] ?? null) ? $seg['hash'] : [];
        $hidden = is_array($hash['campos_hidden'] ?? null) ? $hash['campos_hidden'] : [];
        $tabla = is_array($seg['tabla'] ?? null) ? $seg['tabla'] : [];
        $linksDl = is_array($seg['links_dl'] ?? null) ? $seg['links_dl'] : [];
        $linksOtros = is_array($seg['links_otros'] ?? null) ? $seg['links_otros'] : [];

        return (new ViewNewPhtml('frontend\\actividadcargos\\controller'))->renderizar('select_cargos_personas_en_actividad.phtml', [
            'oTabla' => ListaDesdeDatos::lista($tabla),
            'oBotonesCurso' => new BotonesCurso(PayloadCoercion::int($seg['modo_curso'] ?? 1)),
            'hash_select_html' => FormCargosDeActividadHashCompose::selectListaHiddenHtml([
                'campos_form' => PayloadCoercion::string($hash['campos_form'] ?? ''),
                'campos_no' => PayloadCoercion::string($hash['campos_no'] ?? ''),
                'campos_hidden' => $hidden,
            ]),
            'aLinks_dl' => DossierTipoFormLinkSpecsSigning::signLinkMap($linksDl),
            'aLinks_otros' => DossierTipoFormLinkSpecsSigning::signLinkMap($linksOtros),
            'txt_eliminar' => PayloadCoercion::string($seg['txt_eliminar'] ?? ''),
            'bloque' => PayloadCoercion::string($seg['bloque'] ?? ''),
            'url_form' => PayloadCoercion::string($seg['url_form'] ?? ''),
            'url_cargo_eliminar' => PayloadCoercion::string($seg['url_cargo_eliminar'] ?? ''),
        ], false);
    }
}
