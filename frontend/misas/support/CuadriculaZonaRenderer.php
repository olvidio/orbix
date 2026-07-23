<?php

declare(strict_types=1);

namespace frontend\misas\support;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\AjaxJsonSupport;

/**
 * Renderiza la cuadrícula de zona a partir de datos del backend.
 *
 * - `ver_cuadricula_zona.phtml`: tabla HTML de solo lectura.
 * - `modificar_cuadricula_zona.phtml`: SlickGrid editable con modal SACD.
 */
class CuadriculaZonaRenderer
{
    /**
     * Cuadrícula de solo lectura (ver plan, ver misas zona, …).
     *
     * @param array<int|string, mixed> $data
     * @param array<string, mixed> $post
     * @param array<string, mixed> $overrides
     */
    public static function renderVer(
        array $data,
        array $post,
        string $url_self,
        string $camposSelf,
        array $overrides = []
    ): void {
        self::render($data, $post, $url_self, $camposSelf, 'ver_cuadricula_zona.phtml', false, $overrides);
    }

    /**
     * Cuadrícula editable (modificar plan/plantilla, crear periodo, …).
     *
     * @param array<int|string, mixed> $data
     * @param array<string, mixed> $post
     * @param array<string, mixed> $overrides
     */
    public static function renderModificar(
        array $data,
        array $post,
        string $url_self,
        string $camposSelf,
        array $overrides = []
    ): void {
        self::render($data, $post, $url_self, $camposSelf, 'modificar_cuadricula_zona.phtml', true, $overrides);
    }

    /**
     * @param array<int|string, mixed> $data
     * @param array<string, mixed> $post
     * @param array<string, mixed> $overrides
     */
    private static function render(
        array $data,
        array $post,
        string $url_self,
        string $camposSelf,
        string $plantilla,
        bool $editable,
        array $overrides = []
    ): void {
        $columns_cuadricula = $data['columns_cuadricula'] ?? '[]';
        $json_data_cuadricula = $data['data_cuadricula'] ?? [];

        $oHashSelf = new HashFront();
        $oHashSelf->setUrl($url_self);
        $oHashSelf->setCamposForm($camposSelf);
        $h_ver_cuadricula_zona = $oHashSelf->linkSinValParams();

        $pick = static function (string $key, mixed $defaultFromPost, string $type) use ($data, $overrides): mixed {
            if (array_key_exists($key, $overrides)) {
                return $overrides[$key];
            }
            $value = $data[$key] ?? $defaultFromPost;

            return match ($type) {
                'int' => \frontend\shared\helpers\PayloadCoercion::int($value),
                'string' => \frontend\shared\helpers\PayloadCoercion::string($value),
                default => $value,
            };
        };

        $a_campos = [
            'columns_cuadricula' => $columns_cuadricula,
            'json_data_cuadricula' => $json_data_cuadricula,
            'url_ver_cuadricula_zona' => $url_self,
            'h_ver_cuadricula_zona' => $h_ver_cuadricula_zona,
            'solo_lectura' => !$editable,
            'id_zona' => $pick('id_zona', $post['id_zona'] ?? 0, 'int'),
            'tipo_plantilla' => $pick('tipo_plantilla', $post['tipo_plantilla'] ?? '', 'string'),
            'orden' => $pick('orden', $post['orden'] ?? '', 'string'),
            'seleccion' => $pick('seleccion', $post['seleccion'] ?? 0, 'int'),
            'periodo' => $pick('periodo', $post['periodo'] ?? '', 'string'),
            'empieza_min' => \frontend\shared\helpers\PayloadCoercion::string($data['empieza_min'] ?? $post['empiezamin'] ?? ''),
            'empieza_max' => \frontend\shared\helpers\PayloadCoercion::string($data['empieza_max'] ?? $post['empiezamax'] ?? ''),
            'fila' => $pick('fila', $post['fila'] ?? 0, 'int'),
            'columna' => $pick('columna', $post['columna'] ?? 0, 'int'),
            'h_cuadricula_update' => '',
            'url_cuadricula_update' => '',
            'url_desplegable_sacd' => '',
            'h_desplegable_sacd' => '',
        ];

        if ($editable) {
            $url_cuadricula_update = AppUrlConfig::srcBrowserUrl('/src/misas/cuadricula_update');
            $oHashUpd = new HashFront();
            $oHashUpd->setUrl($url_cuadricula_update);
            $oHashUpd->setCamposForm('dia!id_enc!key!observ!tend!tstart!uuid_item!tipo_plantilla!id_zona');
            $h_cuadricula_update = $oHashUpd->linkSinValParams();

            $url_desplegable_sacd = AppUrlConfig::srcBrowserUrl('/src/misas/desplegable_sacd');
            $oHashDs = new HashFront();
            $oHashDs->setUrl($url_desplegable_sacd);
            $oHashDs->setCamposForm('id_zona!id_sacd!seleccion!dia');
            $h_desplegable_sacd = $oHashDs->linkSinValParams();

            $a_campos['h_cuadricula_update'] = $h_cuadricula_update;
            $a_campos['url_cuadricula_update'] = $url_cuadricula_update;
            $a_campos['url_desplegable_sacd'] = $url_desplegable_sacd;
            $a_campos['h_desplegable_sacd'] = $h_desplegable_sacd;
        }

        AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', $plantilla, $a_campos);
    }
}
