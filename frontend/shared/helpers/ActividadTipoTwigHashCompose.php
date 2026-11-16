<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\security\HashF;

/**
 * Tokens `h` / `h_act` para los Twig de {@see \frontend\actividades\helpers\ActividadTipo}
 * (`linkSinValParams` sobre el POST AJAX a `actividad_tipo_get`, a `actividad_tipo_nom_tipo_tabla.php` y sobre
 * `actividad_ver.php`).
 */
final class ActividadTipoTwigHashCompose
{
    /**
     * @param array<string, mixed> $a_campos Incluye `url` (absoluta hacia `actividad_tipo_get`). `url_act` no interviene en el hash de `h_act`.
     *
     * @return array<string, mixed>
     */
    public static function withHashTokens(array $a_campos): array
    {
        $urlRaw = $a_campos['url'] ?? null;
        $url = is_string($urlRaw) ? $urlRaw : '';
        $oHashTipo = new HashF();
        $oHashTipo->setUrl($url);
        $oHashTipo->setCamposForm('extendida!modo!salida!entrada');
        $a_campos['h'] = $oHashTipo->linkSinValParams();

        $urlNomTipoTablaRaw = $a_campos['url_nom_tipo_tabla'] ?? null;
        if (is_string($urlNomTipoTablaRaw) && $urlNomTipoTablaRaw !== '') {
            $oHashNomTipo = new HashF();
            $oHashNomTipo->setUrl($urlNomTipoTablaRaw);
            $oHashNomTipo->setCamposForm('entrada');
            $a_campos['h_nom_tipo_tabla'] = $oHashNomTipo->linkSinValParams();
        }

        $oHashAct = new HashF();
        $oHashAct->setUrl('frontend/actividades/controller/actividad_ver.php');
        $oHashAct->setCamposForm('id_tipo_activ!refresh');
        $a_campos['h_act'] = $oHashAct->linkSinValParams();

        return $a_campos;
    }
}
