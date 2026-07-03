<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\security\HashFrontSignedLink;

/**
 * Normalización de cabeceras, botones y datos para {@see \frontend\shared\web\Lista}.
 */
final class ActividadesListaSupport
{
    /**
     * @return list<array<string, mixed>|string>
     */
    public static function cabeceras(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (is_string($item)) {
                $out[] = $item;
            } elseif (is_array($item)) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function botones(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @return array<int|string, mixed>
     */
    public static function datos(mixed $raw): array
    {
        return is_array($raw) ? $raw : [];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     cabeceras: list<array<string, mixed>|string>,
     *     botones: list<array<string, mixed>>,
     *     valores: array<int|string, mixed>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'cabeceras' => self::cabeceras($payload['a_cabeceras'] ?? []),
            'botones' => self::botones($payload['a_botones'] ?? []),
            'valores' => self::datos($payload['a_valores'] ?? []),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function signValoresFromPayload(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $rows = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $rows[] = $item;
            }
        }

        return self::signNestedLinkSpecs($rows);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    public static function signNestedLinkSpecs(array $rows): array
    {
        $out = [];
        foreach ($rows as $fila) {
            $row = $fila;
            foreach ($row as $colKey => $cell) {
                if (!is_array($cell) || !isset($cell['link_spec'])) {
                    continue;
                }
                $signed = HashFrontSignedLink::tryFromSpec($cell['link_spec']);
                if ($signed !== '') {
                    $cell['ira'] = $signed;
                }
                unset($cell['link_spec']);
                $row[$colKey] = $cell;
            }
            $out[] = $row;
        }

        return $out;
    }
}
