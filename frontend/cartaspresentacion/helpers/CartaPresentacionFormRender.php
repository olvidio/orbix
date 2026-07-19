<?php

declare(strict_types=1);

namespace frontend\cartaspresentacion\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\helpers\PayloadCoercion;
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
            $url = AppUrlConfig::browserUrlFromAppRelative(
                \frontend\shared\helpers\PayloadCoercion::string($paths['update'] ?? '')
            );
            $oHash = new HashFront();
            $oHash->setUrl($url);
            $hidden = CartaspresentacionPayload::hashCamposHidden($hu['campos_hidden'] ?? []);
            if ($hidden !== []) {
                $oHash->setArrayCamposHidden($hidden);
            }
            $oHash->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hu['campos_form'] ?? ''));
            $payload['hash_update_html'] = $oHash->getCamposHtml();
        } else {
            $payload['hash_update_html'] = \frontend\shared\helpers\PayloadCoercion::string($payload['hash_update_html'] ?? '');
        }

        unset($payload['paths'], $payload['hash_update']);

        return $payload;
    }
}
