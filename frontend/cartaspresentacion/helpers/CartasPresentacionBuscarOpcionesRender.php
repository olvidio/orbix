<?php

declare(strict_types=1);

namespace frontend\cartaspresentacion\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/cartaspresentacion_support.php';

/**
 * Completa el JSON de {@see \src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData} para la vista.
 */
final class CartasPresentacionBuscarOpcionesRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $url_lista = $base . '/' . ltrim(tessera_imprimir_string($paths['lista'] ?? ''), '/');

        $hl = isset($payload['hash_lista']) && is_array($payload['hash_lista']) ? $payload['hash_lista'] : [];
        $oHash = new HashFront();
        $oHash->setUrl($url_lista);
        $hidden = cartaspresentacion_hash_campos_hidden($hl['campos_hidden'] ?? []);
        if ($hidden !== []) {
            $oHash->setArrayCamposHidden($hidden);
        }
        $oHash->setCamposForm(tessera_imprimir_string($hl['campos_form'] ?? ''));
        $oHash->setCamposNo(tessera_imprimir_string($hl['campos_no'] ?? ''));

        $payload['url_lista'] = $url_lista;
        $payload['hash_lista_html'] = $oHash->getCamposHtml();

        unset($payload['paths'], $payload['hash_lista']);

        return $payload;
    }
}
