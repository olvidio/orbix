<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadesPayload
{
public static function statusLabelsFromPayload(array $labelsRow): array
{
    $raw = $labelsRow['id_to_label'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_int($key)) {
            $out[$key] = \frontend\shared\helpers\PayloadCoercion::string($value);
        } elseif (is_string($key)) {
            $out[$key] = \frontend\shared\helpers\PayloadCoercion::string($value);
        }
    }

    return $out;
}

public static function entidadFromVerDatos(array $dataEntidad): array
{
    $entidadRaw = $dataEntidad['entidad'] ?? null;
    if (!is_array($entidadRaw)) {
        die(_('No se encuentra la actividad'));
    }

    return [
        'id_tipo_activ' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['id_tipo_activ'] ?? ''),
        'dl_org' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['dl_org'] ?? ''),
        'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['nom_activ'] ?? ''),
        'id_ubi' => \frontend\shared\helpers\PayloadCoercion::int($entidadRaw['id_ubi'] ?? 0),
        'f_ini' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['f_ini'] ?? ''),
        'h_ini' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['h_ini'] ?? ''),
        'f_fin' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['f_fin'] ?? ''),
        'h_fin' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['h_fin'] ?? ''),
        'precio' => NotasFormSupport::formScalar($entidadRaw['precio'] ?? ''),
        'status' => \frontend\shared\helpers\PayloadCoercion::int($entidadRaw['status'] ?? 0),
        'observ' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['observ'] ?? ''),
        'nivel_stgr' => NotasFormSupport::formScalar($entidadRaw['nivel_stgr'] ?? ''),
        'lugar_esp' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['lugar_esp'] ?? ''),
        'tarifa' => NotasFormSupport::formScalar($entidadRaw['tarifa'] ?? ''),
        'id_repeticion' => \frontend\shared\helpers\PayloadCoercion::int($entidadRaw['id_repeticion'] ?? 0),
        'publicado' => NotasFormSupport::formBoolOrString($entidadRaw['publicado'] ?? ''),
        'plazas' => NotasFormSupport::formScalar($entidadRaw['plazas'] ?? ''),
        'idioma' => \frontend\shared\helpers\PayloadCoercion::string($entidadRaw['idioma'] ?? ''),
    ];
}

public static function verRenderFromPayload(array $data): array
{
    return [
        'html_despl_dl_org' => ActividadesRenderSupport::desplegableHtml(is_array($data['select_dl_org'] ?? null) ? $data['select_dl_org'] : null),
        'html_despl_tarifa' => ActividadesRenderSupport::desplegableHtml(is_array($data['select_tarifa'] ?? null) ? $data['select_tarifa'] : null),
        'html_despl_nivel_stgr' => ActividadesRenderSupport::desplegableHtml(is_array($data['select_nivel_stgr'] ?? null) ? $data['select_nivel_stgr'] : null),
        'html_despl_idioma' => ActividadesRenderSupport::desplegableHtml(is_array($data['select_idioma'] ?? null) ? $data['select_idioma'] : null),
        'html_despl_repeticion' => ActividadesRenderSupport::desplegableHtml(is_array($data['select_repeticion'] ?? null) ? $data['select_repeticion'] : null),
        'nombre_ubi' => \frontend\shared\helpers\PayloadCoercion::string($data['nombre_ubi'] ?? ''),
        'ssfsv' => \frontend\shared\helpers\PayloadCoercion::string($data['ssfsv'] ?? ''),
        'sasistentes' => \frontend\shared\helpers\PayloadCoercion::string($data['sasistentes'] ?? ''),
        'sactividad' => \frontend\shared\helpers\PayloadCoercion::string($data['sactividad'] ?? ''),
        'snom_tipo' => \frontend\shared\helpers\PayloadCoercion::string($data['snom_tipo'] ?? ''),
        'isfsv' => \frontend\shared\helpers\PayloadCoercion::int($data['isfsv'] ?? 0),
        'tarifa_inicial' => $data['tarifa_inicial'] ?? null,
    ];
}

public static function permisoCrearFromRow(array $row): ?array
{
    $crear = $row['permiso_crear'] ?? false;
    if ($crear === false || !is_array($crear)) {
        return null;
    }

    return [
        'of_responsable_txt' => \frontend\shared\helpers\PayloadCoercion::string($crear['of_responsable_txt'] ?? ''),
        'status' => \frontend\shared\helpers\PayloadCoercion::int($crear['status'] ?? 0),
    ];
}

public static function listaValoresFromPayload(mixed $raw): array
{
    return ActividadesListaSupport::signValoresFromPayload($raw);
}

public static function fasesCompletadasFromPayload(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $id) {
        if (is_int($id) || is_string($id)) {
            $out[] = \frontend\shared\helpers\PayloadCoercion::int($id);
        }
    }

    return $out;
}
}
