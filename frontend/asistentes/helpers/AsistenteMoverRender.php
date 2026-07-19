<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;

/**
 * Completa el JSON de {@see \src\asistentes\application\AsistenteMoverData} para la vista.
 */
final class AsistenteMoverRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $payload['url_guardar'] = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\FuncTablasSupport::payloadString($paths, 'guardar')
        );

        $hm = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        if ($hm !== []) {
            $oHash = new HashFront();
            $cn = \frontend\shared\helpers\FuncTablasSupport::payloadString($hm, 'campos_no');
            if ($cn !== '') {
                $oHash->setCamposNo($cn);
            }
            $oHash->setCamposForm(\frontend\shared\helpers\FuncTablasSupport::payloadString($hm, 'campos_form'));
            $hidden = AsistentesRenderSupport::hashCamposHidden($hm['campos_hidden'] ?? []);
            $oHash->setArrayCamposHidden($hidden);
            $payload['hash_campos_html'] = $oHash->getCamposHtml();

            $opciones = NotasFormSupport::desplegableOpciones($payload['opciones_actividades'] ?? []);
            $oDespl = new Desplegable();
            $oDespl->setNombre('id_activ');
            $oDespl->setOpciones($opciones);
            $payload['desplegable_actividades_html'] = $oDespl->desplegable();
        } else {
            $payload['hash_campos_html'] = '';
            $payload['desplegable_actividades_html'] = '';
        }

        unset($payload['paths'], $payload['hash_main'], $payload['opciones_actividades']);

        return $payload;
    }
}
