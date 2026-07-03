<?php

declare(strict_types=1);

namespace frontend\configuracion\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ConfiguracionPayload
{
    /**
     * @param array<int|string, mixed> $raw
     * @return array<string, mixed>
     */
    public static function stringKeyPayload(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
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
        foreach ($raw as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function modulosFormViewFromPayload(array $payload): array
    {
        return [
            'hash_form_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['hash_form_html'] ?? ''),
            'hash_actualizar_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['hash_actualizar_html'] ?? ''),
            'id_mod' => \frontend\shared\helpers\PayloadCoercion::int($payload['id_mod'] ?? 0),
            'nom' => \frontend\shared\helpers\PayloadCoercion::string($payload['nom'] ?? ''),
            'descripcion' => \frontend\shared\helpers\PayloadCoercion::string($payload['descripcion'] ?? ''),
            'a_mods_todos' => is_array($payload['a_mods_todos'] ?? null) ? $payload['a_mods_todos'] : [],
            'a_apps_todas' => is_array($payload['a_apps_todas'] ?? null) ? $payload['a_apps_todas'] : [],
            'a_mods_req' => is_array($payload['a_mods_req'] ?? null) ? $payload['a_mods_req'] : [],
            'a_apps_req' => is_array($payload['a_apps_req'] ?? null) ? $payload['a_apps_req'] : [],
            'a_apps_mod' => is_array($payload['a_apps_mod'] ?? null) ? $payload['a_apps_mod'] : [],
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     hash_lista_html: string,
     *     a_cabeceras: list<array<string, mixed>|string>,
     *     a_botones: list<array<string, mixed>>,
     *     a_valores: array<int|string, mixed>,
     *     txt_eliminar: string,
     *     txt_anadir_modulo: string,
     * }
     */
    public static function modulosSelectViewFromPayload(array $payload): array
    {
        return [
            'hash_lista_html' => \frontend\shared\helpers\PayloadCoercion::string($payload['hash_lista_html'] ?? ''),
            'a_cabeceras' => ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []),
            'a_botones' => ActividadesListaSupport::botones($payload['a_botones'] ?? []),
            'a_valores' => ActividadesListaSupport::datos($payload['a_valores'] ?? []),
            'txt_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($payload['txt_eliminar'] ?? ''),
            'txt_anadir_modulo' => \frontend\shared\helpers\PayloadCoercion::string($payload['txt_anadir_modulo'] ?? ''),
        ];
    }

    /**
     * @param array<int|string, mixed> $data
     * @return array{a_locales: array<int|string, string>, idioma_select: string}
     */
    public static function parametrosIdiomaDesplegable(array $data): array
    {
        return [
            'a_locales' => NotasFormSupport::desplegableOpciones($data['a_locales'] ?? []),
            'idioma_select' => \frontend\shared\helpers\PayloadCoercion::string($data['idioma_select'] ?? ''),
        ];
    }

    /**
     * @param array<int|string, mixed> $data
     * @return array<string, mixed>
     */
    public static function parametrosViewFromPayload(array $data): array
    {
        $view = self::stringKeyPayload($data);
        $idioma = self::parametrosIdiomaDesplegable($data);
        $view['a_locales'] = $idioma['a_locales'];
        $view['idioma_select'] = $idioma['idioma_select'];

        return $view;
    }
}
