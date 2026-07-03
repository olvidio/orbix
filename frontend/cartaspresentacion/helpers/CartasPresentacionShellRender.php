<?php

declare(strict_types=1);

namespace frontend\cartaspresentacion\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Completa el JSON de {@see \src\cartaspresentacion\application\CartasPresentacionShellData} para la vista.
 */
final class CartasPresentacionShellRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $abs = static function (string $rel) use ($base): string {
            return $rel !== '' ? $base . '/' . ltrim($rel, '/') : '';
        };

        $url_ctr = $abs(\frontend\shared\helpers\PayloadCoercion::string($paths['ctr'] ?? ''));
        $hc = isset($payload['hash_ctr']) && is_array($payload['hash_ctr']) ? $payload['hash_ctr'] : [];
        $oHashCtr = new HashFront();
        $oHashCtr->setUrl($url_ctr);
        $oHashCtr->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hc['campos_form'] ?? ''));
        $h_ctr = $oHashCtr->linkSinValParams();

        $url_lista = $abs(\frontend\shared\helpers\PayloadCoercion::string($paths['lista'] ?? ''));
        $hl = isset($payload['hash_lista']) && is_array($payload['hash_lista']) ? $payload['hash_lista'] : [];
        $oHashLista = new HashFront();
        $oHashLista->setUrl($url_lista);
        $oHashLista->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hl['campos_form'] ?? ''));
        $oHashLista->setCamposNo(\frontend\shared\helpers\PayloadCoercion::string($hl['campos_no'] ?? ''));
        $hash_lista_html = $oHashLista->getCamposHtml();

        $url_form = $abs(\frontend\shared\helpers\PayloadCoercion::string($paths['form'] ?? ''));
        $hf = isset($payload['hash_form']) && is_array($payload['hash_form']) ? $payload['hash_form'] : [];
        $oHashForm = new HashFront();
        $oHashForm->setUrl($url_form);
        $oHashForm->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hf['campos_form'] ?? ''));
        $h_form = $oHashForm->linkSinVal();

        $url_poblaciones = $abs(\frontend\shared\helpers\PayloadCoercion::string($paths['poblaciones'] ?? ''));
        $hp = isset($payload['hash_poblaciones']) && is_array($payload['hash_poblaciones']) ? $payload['hash_poblaciones'] : [];
        $oHashPob = new HashFront();
        $oHashPob->setUrl($url_poblaciones);
        $oHashPob->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hp['campos_form'] ?? ''));
        $h_poblaciones = $oHashPob->linkSinValParams();

        $url_eliminar = $abs(\frontend\shared\helpers\PayloadCoercion::string($paths['eliminar'] ?? ''));
        $he = isset($payload['hash_eliminar']) && is_array($payload['hash_eliminar']) ? $payload['hash_eliminar'] : [];
        $oHashEliminar = new HashFront();
        $oHashEliminar->setUrl($url_eliminar);
        $oHashEliminar->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($he['campos_form'] ?? ''));
        $h_eliminar = $oHashEliminar->linkSinValParams();

        $payload['url_ctr'] = $url_ctr;
        $payload['h_ctr'] = $h_ctr;
        $payload['url_lista'] = $url_lista;
        $payload['hash_lista_html'] = $hash_lista_html;
        $payload['url_form'] = $url_form;
        $payload['h_form'] = $h_form;
        $payload['url_poblaciones'] = $url_poblaciones;
        $payload['h_poblaciones'] = $h_poblaciones;
        $payload['url_update'] = $abs(\frontend\shared\helpers\PayloadCoercion::string($paths['update'] ?? ''));
        $payload['url_eliminar'] = $url_eliminar;
        $payload['h_eliminar'] = $h_eliminar;

        unset(
            $payload['paths'],
            $payload['hash_ctr'],
            $payload['hash_lista'],
            $payload['hash_form'],
            $payload['hash_poblaciones'],
            $payload['hash_eliminar'],
        );

        return $payload;
    }
}
