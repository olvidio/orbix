<?php

declare(strict_types=1);

namespace frontend\cartaspresentacion\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class CartaspresentacionPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function postData(mixed $data): array
    {
        if (!is_array($data)) {
            return [];
        }
        $out = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    public static function hashCamposHidden(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $k => $v) {
            if (is_string($k)) {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     mi_dele: string,
     *     url_ctr: string,
     *     h_ctr: string,
     *     url_lista: string,
     *     hash_lista_html: string,
     *     url_form: string,
     *     h_form: string,
     *     url_poblaciones: string,
     *     h_poblaciones: string,
     *     url_update: string,
     *     url_eliminar: string,
     *     h_eliminar: string,
     * }
     */
    public static function shellViewFromPayload(array $payload): array
    {
        return [
            'mi_dele' => \frontend\shared\helpers\PayloadCoercion::string($payload['mi_dele'] ?? ''),
            'url_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_ctr'] ?? ''),
            'h_ctr' => \frontend\shared\helpers\PayloadCoercion::string($payload['h_ctr'] ?? ''),
            'url_lista' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_lista'] ?? ''),
            'hash_lista_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['hash_lista_html'] ?? ''),
            'url_form' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_form'] ?? ''),
            'h_form' => \frontend\shared\helpers\PayloadCoercion::string($payload['h_form'] ?? ''),
            'url_poblaciones' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_poblaciones'] ?? ''),
            'h_poblaciones' => \frontend\shared\helpers\PayloadCoercion::string($payload['h_poblaciones'] ?? ''),
            'url_update' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_update'] ?? ''),
            'url_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_eliminar'] ?? ''),
            'h_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($payload['h_eliminar'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     url_lista: string,
     *     hash_lista_html: string,
     *     opciones_region: array<int|string, string>,
     *     opciones_pais: array<int|string, string>,
     *     opciones_delegacion: array<int|string, string>,
     * }
     */
    public static function buscarViewFromPayload(array $payload): array
    {
        return [
            'url_lista' => \frontend\shared\helpers\PayloadCoercion::string($payload['url_lista'] ?? ''),
            'hash_lista_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['hash_lista_html'] ?? ''),
            'opciones_region' => NotasFormSupport::desplegableOpciones($payload['opciones_region'] ?? []),
            'opciones_pais' => NotasFormSupport::desplegableOpciones($payload['opciones_pais'] ?? []),
            'opciones_delegacion' => NotasFormSupport::desplegableOpciones($payload['opciones_delegacion'] ?? []),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     ok: bool,
     *     mensaje: string,
     *     nombre_ubi: string,
     *     pres_nom: string,
     *     pres_telf: string,
     *     pres_mail: string,
     *     zona: string,
     *     observ: string,
     *     hash_update_html: string,
     * }
     */
    public static function formViewFromPayload(array $payload): array
    {
        return [
            'ok' => !empty($payload['ok']),
            'mensaje' => \frontend\shared\helpers\PayloadCoercion::string($payload['mensaje'] ?? ''),
            'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($payload['nombre_ubi'] ?? ''),
            'pres_nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['pres_nom'] ?? ''),
            'pres_telf' => \frontend\shared\helpers\PayloadCoercion::string($payload['pres_telf'] ?? ''),
            'pres_mail' => \frontend\shared\helpers\PayloadCoercion::string($payload['pres_mail'] ?? ''),
            'zona' => \frontend\shared\helpers\PayloadCoercion::string($payload['zona'] ?? ''),
            'observ' => \frontend\shared\helpers\PayloadCoercion::string($payload['observ'] ?? ''),
            'hash_update_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['hash_update_html'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{html_lista: string, html_errores: string}
     */
    public static function listaHtmlFromPayload(array $payload): array
    {
        return [
            'html_lista' => \frontend\shared\helpers\PayloadCoercion::string($payload['html_lista'] ?? ''),
            'html_errores' => \frontend\shared\helpers\PayloadCoercion::string($payload['html_errores'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{cabeceras: list<array<string, mixed>|string>, valores: array<int|string, mixed>, explicacion: string}
     */
    public static function ubisListaFromPayload(array $payload): array
    {
        return [
            'cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
            'explicacion' => \frontend\shared\helpers\PayloadCoercion::string($payload['explicacion'] ?? ''),
        ];
    }
}
