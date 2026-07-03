<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;

/**
 * Normalización de segment data para Renders de dossiers actividadestudios.
 */
final class ActividadestudiosRenderSupport
{
    /**
     * @return array<string, mixed>
     */
    public static function stringKeyRow(mixed $raw): array
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
     * @return array{path: string, query?: array<string, mixed>}|null
     */
    public static function linkSpec(mixed $spec): ?array
    {
        if (!is_array($spec)) {
            return null;
        }
        $path = \frontend\shared\helpers\PayloadCoercion::string($spec['path'] ?? '');
        if ($path === '') {
            return null;
        }
        $parsed = ['path' => $path];
        $query = $spec['query'] ?? null;
        if (is_array($query)) {
            $q = [];
            foreach ($query as $k => $v) {
                if (is_string($k)) {
                    $q[$k] = $v;
                }
            }
            if ($q !== []) {
                $parsed['query'] = $q;
            }
        }

        return $parsed;
    }

    /**
     * @return list<string>
     */
    public static function avisoLines(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $line) {
            $out[] = \frontend\shared\helpers\PayloadCoercion::string($line);
        }

        return $out;
    }

    /**
     * @return array<int|string, string>
     */
    public static function listaGrupos(mixed $raw): array
    {
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
}
