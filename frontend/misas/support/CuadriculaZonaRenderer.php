<?php

declare(strict_types=1);

namespace frontend\misas\support;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;

/**
 * Renderiza `ver_cuadricula_zona.phtml` a partir de los datos devueltos por
 * un endpoint backend (`/src/misas/ver_cuadricula_zona_data`,
 * `/src/misas/crear_nuevo_periodo_data`, `/src/misas/ver_misas_zona_data`, ...).
 *
 * Encapsula la construcción de los `Hash` y la composición de `$a_campos`,
 * que antes estaban duplicados en `ver_cuadricula_zona.php`,
 * `modificar_cuadricula_zona.php`, `crear_nuevo_periodo.php` y
 * `ver_misas_zona.php`.
 */
class CuadriculaZonaRenderer
{
    /**
     * @param array<string, mixed> $data       respuesta del backend (JSON decodificado).
     * @param array<string, mixed> $post       input normalizado del controlador frontend.
     * @param string               $url_self   URL (relativa) del propio controlador para el hash "self".
     * @param string               $camposSelf lista de campos del hash "self" separados por `!`.
     * @param array<string, mixed> $overrides  valores fijos que deben sobreescribir `$data` y `$post`
     *                                         (por ejemplo `['tipo_plantilla' => 'p']`).
     */
    public static function render(
        array $data,
        array $post,
        string $url_self,
        string $camposSelf,
        array $overrides = []
    ): void {
        $columns_cuadricula = $data['columns_cuadricula'] ?? '[]';
        $json_data_cuadricula = $data['data_cuadricula'] ?? [];

        $url_cuadricula_update = AppUrlConfig::getApiBaseUrl() . '/src/misas/cuadricula_update';
        $oHashUpd = new Hash();
        $oHashUpd->setUrl($url_cuadricula_update);
        $oHashUpd->setCamposForm('dia!id_enc!key!observ!tend!tstart!uuid_item!tipo_plantilla!id_zona');
        $h_cuadricula_update = $oHashUpd->linkSinValParams();

        $url_desplegable_sacd = AppUrlConfig::getApiBaseUrl() . '/src/misas/desplegable_sacd';
        $oHashDs = new Hash();
        $oHashDs->setUrl($url_desplegable_sacd);
        $oHashDs->setCamposForm('id_zona!id_sacd!seleccion!dia');
        $h_desplegable_sacd = $oHashDs->linkSinValParams();

        $oHashSelf = new Hash();
        $oHashSelf->setUrl($url_self);
        $oHashSelf->setCamposForm($camposSelf);
        $h_ver_cuadricula_zona = $oHashSelf->linkSinValParams();

        $pick = static function (string $key, mixed $defaultFromPost, string $type) use ($data, $overrides): mixed {
            if (array_key_exists($key, $overrides)) {
                return $overrides[$key];
            }
            $value = $data[$key] ?? $defaultFromPost;

            return match ($type) {
                'int' => (int)$value,
                'string' => (string)$value,
                default => $value,
            };
        };

        $a_campos = [
            'columns_cuadricula' => $columns_cuadricula,
            'json_data_cuadricula' => $json_data_cuadricula,
            'url_desplegable_sacd' => $url_desplegable_sacd,
            'h_desplegable_sacd' => $h_desplegable_sacd,
            'url_ver_cuadricula_zona' => $url_self,
            'h_ver_cuadricula_zona' => $h_ver_cuadricula_zona,
            'id_zona' => $pick('id_zona', $post['id_zona'] ?? 0, 'int'),
            'tipo_plantilla' => $pick('tipo_plantilla', $post['tipo_plantilla'] ?? '', 'string'),
            'orden' => $pick('orden', $post['orden'] ?? '', 'string'),
            'seleccion' => $pick('seleccion', $post['seleccion'] ?? 0, 'int'),
            'periodo' => $pick('periodo', $post['periodo'] ?? '', 'string'),
            'empieza_min' => (string)($data['empieza_min'] ?? $post['empiezamin'] ?? ''),
            'empieza_max' => (string)($data['empieza_max'] ?? $post['empiezamax'] ?? ''),
            'fila' => $pick('fila', $post['fila'] ?? 0, 'int'),
            'columna' => $pick('columna', $post['columna'] ?? 0, 'int'),
            'h_cuadricula_update' => $h_cuadricula_update,
            'url_cuadricula_update' => $url_cuadricula_update,
        ];

        $oView = new ViewNewPhtml('frontend\\misas\\controller');
        $oView->renderizar('ver_cuadricula_zona.phtml', $a_campos);
    }
}
