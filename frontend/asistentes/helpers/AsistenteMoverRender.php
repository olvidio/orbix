<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

require_once __DIR__ . '/asistentes_support.php';

use function frontend\shared\helpers\payload_string;

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
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $guardarRel = payload_string($paths, 'guardar');
        $payload['url_guardar'] = $guardarRel !== '' ? $base . '/' . ltrim($guardarRel, '/') : '';

        $hm = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        if ($hm !== []) {
            $oHash = new HashFront();
            $cn = payload_string($hm, 'campos_no');
            if ($cn !== '') {
                $oHash->setCamposNo($cn);
            }
            $oHash->setCamposForm(payload_string($hm, 'campos_form'));
            $hidden = asistentes_hash_campos_hidden($hm['campos_hidden'] ?? []);
            $oHash->setArrayCamposHidden($hidden);
            $payload['hash_campos_html'] = $oHash->getCamposHtml();

            $opciones = notas_desplegable_opciones($payload['opciones_actividades'] ?? []);
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
