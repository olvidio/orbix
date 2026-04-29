<?php

declare(strict_types=1);

namespace frontend\cartaspresentacion\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Completa el JSON de {@see \src\cartaspresentacion\application\CartaPresentacionFormData} para la vista.
 */
final class CartaPresentacionFormRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $ok = (bool)($payload['ok'] ?? false);
        $hu = isset($payload['hash_update']) && is_array($payload['hash_update']) ? $payload['hash_update'] : [];
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];

        if ($ok && $hu !== []) {
            $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
            $rel = (string)($paths['update'] ?? '');
            $url = $rel !== '' ? $base . '/' . ltrim($rel, '/') : '';
            $oHash = new HashFront();
            $oHash->setUrl($url);
            $hidden = $hu['campos_hidden'] ?? [];
            if (is_array($hidden) && $hidden !== []) {
                $oHash->setArrayCamposHidden($hidden);
            }
            $oHash->setCamposForm((string)($hu['campos_form'] ?? ''));
            $payload['hash_update_html'] = $oHash->getCamposHtml();
        } else {
            $payload['hash_update_html'] = (string)($payload['hash_update_html'] ?? '');
        }

        unset($payload['paths'], $payload['hash_update']);

        return $payload;
    }
}
