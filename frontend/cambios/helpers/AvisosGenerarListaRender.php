<?php

declare(strict_types=1);

namespace frontend\cambios\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\actividades\helpers\ActividadTipo;
use frontend\actividades\helpers\TiposDeActividades;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Completa el JSON de {@see \src\cambios\application\AvisosGenerarListaData} para la vista.
 */
final class AvisosGenerarListaRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $id_usuario = \frontend\shared\helpers\PayloadCoercion::int($payload['effective_id_usuario'] ?? 0);
        if ($id_usuario === 0) {
            $payload['url_eliminar'] = '';
            $payload['url_eliminar_fecha'] = '';
            $payload['h_eliminar'] = '';
            $payload['h_eliminar_fecha'] = '';

            return $payload;
        }

        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];
        $url_eliminar = $base . '/' . ltrim(\frontend\shared\helpers\PayloadCoercion::string($paths['eliminar'] ?? ''), '/');
        $url_eliminar_fecha = $base . '/' . ltrim(\frontend\shared\helpers\PayloadCoercion::string($paths['eliminar_fecha'] ?? ''), '/');

        $he = isset($payload['hash_eliminar']) && is_array($payload['hash_eliminar']) ? $payload['hash_eliminar'] : [];
        $oHashElim = new HashFront();
        $oHashElim->setUrl($url_eliminar);
        $cn = \frontend\shared\helpers\PayloadCoercion::string($he['campos_no'] ?? '');
        if ($cn !== '') {
            $oHashElim->setCamposNo($cn);
        }
        $h_eliminar = $oHashElim->linkSinValParams();

        $hef = isset($payload['hash_eliminar_fecha']) && is_array($payload['hash_eliminar_fecha']) ? $payload['hash_eliminar_fecha'] : [];
        $oHashElimF = new HashFront();
        $oHashElimF->setUrl($url_eliminar_fecha);
        $oHashElimF->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hef['campos_form'] ?? ''));
        $h_eliminar_fecha = $oHashElimF->linkSinValParams();

        $payload['url_eliminar'] = $url_eliminar;
        $payload['url_eliminar_fecha'] = $url_eliminar_fecha;
        $payload['h_eliminar'] = $h_eliminar;
        $payload['h_eliminar_fecha'] = $h_eliminar_fecha;

        unset($payload['paths'], $payload['hash_eliminar'], $payload['hash_eliminar_fecha']);

        return $payload;
    }
}
