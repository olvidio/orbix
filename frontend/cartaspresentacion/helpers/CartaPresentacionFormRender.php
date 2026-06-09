<?php

declare(strict_types=1);

namespace frontend\cartaspresentacion\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/cartaspresentacion_support.php';

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
            $rel = tessera_imprimir_string($paths['update'] ?? '');
            $url = $rel !== '' ? $base . '/' . ltrim($rel, '/') : '';
            $oHash = new HashFront();
            $oHash->setUrl($url);
            $hidden = cartaspresentacion_hash_campos_hidden($hu['campos_hidden'] ?? []);
            if ($hidden !== []) {
                $oHash->setArrayCamposHidden($hidden);
            }
            $oHash->setCamposForm(tessera_imprimir_string($hu['campos_form'] ?? ''));
            $payload['hash_update_html'] = $oHash->getCamposHtml();
        } else {
            $payload['hash_update_html'] = tessera_imprimir_string($payload['hash_update_html'] ?? '');
        }

        unset($payload['paths'], $payload['hash_update']);

        return $payload;
    }
}
