<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Hash, enlaces firmados y formularios de periodo en vistas asistentes.
 */
final class AsistentesRenderSupport
{
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
     * @return array<string, string>
     */
    public static function signLinkMap(mixed $raw): array
    {
        return DossierTipoFormLinkSpecsSigning::signLinkMap(self::linkSpecsByLabel($raw));
    }

    /**
     * @return array{form_name: string, titulo: string, opciones_periodos: array<int|string, string>, periodo_sel: string, year_sel: string}|null
     */
    public static function periodoFormConfig(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
        }

        return [
            'form_name' => \frontend\shared\helpers\PayloadCoercion::string($raw['form_name'] ?? 'modifica'),
            'titulo' => \frontend\shared\helpers\PayloadCoercion::string($raw['titulo'] ?? ''),
            'opciones_periodos' => NotasFormSupport::desplegableOpciones($raw['opciones_periodos'] ?? []),
            'periodo_sel' => \frontend\shared\helpers\PayloadCoercion::string($raw['periodo_sel'] ?? 'tot_any'),
            'year_sel' => \frontend\shared\helpers\PayloadCoercion::string($raw['year_sel'] ?? (string) date('Y')),
        ];
    }

    /**
     * @return array<string, array{path: string, query?: array<string, mixed>}>
     */
    private static function linkSpecsByLabel(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $label => $spec) {
            if (!is_string($label) || !is_array($spec)) {
                continue;
            }
            $path = $spec['path'] ?? null;
            if (!is_string($path) || $path === '') {
                continue;
            }
            $entry = ['path' => $path];
            $query = $spec['query'] ?? null;
            if (is_array($query)) {
                $q = [];
                foreach ($query as $k => $v) {
                    $q[(string) $k] = $v;
                }
                $entry['query'] = $q;
            }
            $out[$label] = $entry;
        }

        return $out;
    }
}
