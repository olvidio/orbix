<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadesPayload
{
    /**
     * @return array<string, mixed>|null
     */
    private static function optionalStringKeyedArray(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
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
     * @param array<string, mixed> $labelsRow
     * @return array<int|string, string>
     */
    public static function statusLabelsFromPayload(array $labelsRow): array
    {
        $raw = $labelsRow['id_to_label'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $value) {
            if (is_int($key)) {
                $out[$key] = PayloadCoercion::string($value);
            } elseif (is_string($key)) {
                $out[$key] = PayloadCoercion::string($value);
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $dataEntidad
     * @return array<string, mixed>
     */
    public static function entidadFromVerDatos(array $dataEntidad): array
    {
        $entidadRaw = $dataEntidad['entidad'] ?? null;
        if (!is_array($entidadRaw)) {
            die(_('No se encuentra la actividad'));
        }

        return [
            'id_tipo_activ' => PayloadCoercion::string($entidadRaw['id_tipo_activ'] ?? ''),
            'dl_org' => PayloadCoercion::string($entidadRaw['dl_org'] ?? ''),
            'nom_activ' => PayloadCoercion::string($entidadRaw['nom_activ'] ?? ''),
            'id_ubi' => PayloadCoercion::int($entidadRaw['id_ubi'] ?? 0),
            'f_ini' => PayloadCoercion::string($entidadRaw['f_ini'] ?? ''),
            'h_ini' => PayloadCoercion::string($entidadRaw['h_ini'] ?? ''),
            'f_fin' => PayloadCoercion::string($entidadRaw['f_fin'] ?? ''),
            'h_fin' => PayloadCoercion::string($entidadRaw['h_fin'] ?? ''),
            'precio' => NotasFormSupport::formScalar($entidadRaw['precio'] ?? ''),
            'status' => PayloadCoercion::int($entidadRaw['status'] ?? 0),
            'observ' => PayloadCoercion::string($entidadRaw['observ'] ?? ''),
            'nivel_stgr' => NotasFormSupport::formScalar($entidadRaw['nivel_stgr'] ?? ''),
            'lugar_esp' => PayloadCoercion::string($entidadRaw['lugar_esp'] ?? ''),
            'tarifa' => NotasFormSupport::formScalar($entidadRaw['tarifa'] ?? ''),
            'id_repeticion' => PayloadCoercion::int($entidadRaw['id_repeticion'] ?? 0),
            'publicado' => NotasFormSupport::formBoolOrString($entidadRaw['publicado'] ?? ''),
            'plazas' => NotasFormSupport::formScalar($entidadRaw['plazas'] ?? ''),
            'idioma' => PayloadCoercion::string($entidadRaw['idioma'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function verRenderFromPayload(array $data): array
    {
        return [
            'html_despl_dl_org' => ActividadesRenderSupport::desplegableHtml(self::optionalStringKeyedArray($data['select_dl_org'] ?? null)),
            'html_despl_tarifa' => ActividadesRenderSupport::desplegableHtml(self::optionalStringKeyedArray($data['select_tarifa'] ?? null)),
            'html_despl_nivel_stgr' => ActividadesRenderSupport::desplegableHtml(self::optionalStringKeyedArray($data['select_nivel_stgr'] ?? null)),
            'html_despl_idioma' => ActividadesRenderSupport::desplegableHtml(self::optionalStringKeyedArray($data['select_idioma'] ?? null)),
            'html_despl_repeticion' => ActividadesRenderSupport::desplegableHtml(self::optionalStringKeyedArray($data['select_repeticion'] ?? null)),
            'nombre_ubi' => PayloadCoercion::string($data['nombre_ubi'] ?? ''),
            'ssfsv' => PayloadCoercion::string($data['ssfsv'] ?? ''),
            'sasistentes' => PayloadCoercion::string($data['sasistentes'] ?? ''),
            'sactividad' => PayloadCoercion::string($data['sactividad'] ?? ''),
            'snom_tipo' => PayloadCoercion::string($data['snom_tipo'] ?? ''),
            'isfsv' => PayloadCoercion::int($data['isfsv'] ?? 0),
            'tarifa_inicial' => $data['tarifa_inicial'] ?? null,
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{of_responsable_txt: string, status: int}|null
     */
    public static function permisoCrearFromRow(array $row): ?array
    {
        $crear = $row['permiso_crear'] ?? false;
        if ($crear === false || !is_array($crear)) {
            return null;
        }

        return [
            'of_responsable_txt' => PayloadCoercion::string($crear['of_responsable_txt'] ?? ''),
            'status' => PayloadCoercion::int($crear['status'] ?? 0),
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    public static function listaValoresFromPayload(mixed $raw): array
    {
        return ActividadesListaSupport::signValoresFromPayload($raw);
    }

    /**
     * @return list<int>
     */
    public static function fasesCompletadasFromPayload(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $id) {
            if (is_int($id) || is_string($id)) {
                $out[] = PayloadCoercion::int($id);
            }
        }

        return $out;
    }
}
