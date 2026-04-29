<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Completa el JSON de {@see \src\asistentes\application\ListaUltimQueCtrData} para la vista.
 */
final class ListaUltimQueCtrRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $formRel = (string)($paths['form_action'] ?? '');
        $payload['form_action'] = $formRel !== '' ? $base . '/' . ltrim($formRel, '/') : '';

        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm((string)($hashMain['campos_form'] ?? 'id_ubi'));
        $cn = (string)($hashMain['campos_no'] ?? '');
        if ($cn !== '') {
            $oHash->setCamposNo($cn);
        }
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);
        $payload['hash_form_html'] = $oHash->getCamposHtml();

        unset($payload['hash_main'], $payload['paths']);

        return $payload;
    }
}
