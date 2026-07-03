<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

final class ActividadesMutacionSupport
{
public static function calendarioFormHashCamposForm(): string
{
    return 'dl_org!f_fin!f_ini!h_fin!h_ini!extendida!iactividad_val!iasistentes_val!id_repeticion!id_ubi!inom_tipo_val!isfsv_val!lugar_esp!nivel_stgr!nom_activ!nombre_ubi!observ!plazas!precio!publicado!status!id_tarifa!idioma';
}

public static function verFormHashCamposForm(): string
{
    return 'status!dl_org!f_fin!f_ini!h_fin!h_ini!id_repeticion!id_ubi!lugar_esp!mod!nivel_stgr!nom_activ!nombre_ubi!observ!precio!id_tarifa!publicado!plazas!idioma'
        . '!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val'
        . '!sactividad!sasistentes!snom_tipo';
}

public static function mutacionAjaxAllowedPostKeys(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $fields = array_merge(
        array_filter(explode('!', self::calendarioFormHashCamposForm())),
        array_filter(explode('!', self::verFormHashCamposForm())),
        ['id_tipo_activ', 'id_activ', 'id_ubi', 'ssfsv', 'mod', 'id_tarifa'],
        ['h', 'hh', 'hhc', 'hno', 'hchk', 'hnov', 'horig', 'hhorig', 'PHPSESSID'],
    );

    $cache = array_fill_keys(array_unique($fields), true);

    return $cache;
}

public static function calendarioMutacionSerializeAllowJson(): string
{
    $fields = array_merge(
        array_filter(explode('!', self::calendarioFormHashCamposForm())),
        ['id_tipo_activ', 'id_activ', 'id_ubi', 'ssfsv', 'mod'],
        ['h', 'hh', 'hhc', 'hno', 'hchk', 'hnov', 'horig', 'hhorig'],
    );

    return json_encode(array_fill_keys(array_unique($fields), 1), JSON_UNESCAPED_UNICODE);
}

public static function mutacionAjaxSanitizePost(): void
{
    if ($_POST === []) {
        return;
    }

    $allowed = self::mutacionAjaxAllowedPostKeys();
    foreach (array_keys($_POST) as $key) {
        if (!isset($allowed[$key]) && !str_starts_with($key, 'scroll_id_')) {
            unset($_POST[$key]);
        }
    }
}
}
