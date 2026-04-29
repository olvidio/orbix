<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\security\HashFront;

/**
 * Tokens `h` / `h_act` para los Twig de {@see \src\actividades\application\ActividadTipo}
 * (`linkSinValParams` sobre el POST AJAX a `actividad_tipo_get` y sobre `actividad_ver.php`).
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
        $url = (string)($a_campos['url'] ?? '');
        $oHashTipo = new HashFront();
        $oHashTipo->setUrl($url);
        $oHashTipo->setCamposForm('extendida!modo!salida!entrada');
        $a_campos['h'] = $oHashTipo->linkSinValParams();

        $oHashAct = new HashFront();
        $oHashAct->setUrl('frontend/actividades/controller/actividad_ver.php');
        $oHashAct->setCamposForm('id_tipo_activ!refresh');
        $a_campos['h_act'] = $oHashAct->linkSinValParams();

        return $a_campos;
    }
}
