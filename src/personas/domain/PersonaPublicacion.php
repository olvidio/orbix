<?php

declare(strict_types=1);

namespace src\personas\domain;

use JsonException;
use src\shared\infrastructure\persistence\ConverterJson;

/**
 * Publicación cross-DL (caso B): un solo jsonb mapa DL → caducidad.
 *
 * Lectura/escritura vía ConverterJson (mismo criterio que cedidas, json_certificados, etc.).
 *
 * Ejemplo: {"dlp":"2026-07-13 00:00:00+00:00","crArg":"2026-08-03 00:00:00+00:00"}
 * Sin caducidad (p.ej. "*"): {"*":null}
 */
final class PersonaPublicacion
{
    /** Valor en publicado_para: visible para todas las DL (sin caducidad). */
    public const DL_TODAS = '*';

    /** TTL por defecto al publicar para una DL concreta. */
    public const TTL_DEFAULT = 'P1M';

    /**
     * Código de DL sin sufijo de esquema sv/sf (`dlbv`/`dlbf` → `dlb`).
     * Alineado con ConfigGlobal::mi_dele() y el filtro de v_personas_pub.
     */
    public static function normalizarDl(string $dl): string
    {
        $dl = trim($dl);
        if ($dl === '' || $dl === self::DL_TODAS) {
            return $dl;
        }
        $last = substr($dl, -1);

        return ($last === 'v' || $last === 'f') ? substr($dl, 0, -1) : $dl;
    }

    public static function fechaHastaDefault(?\DateTimeImmutable $desde = null): \DateTimeImmutable
    {
        $base = $desde ?? new \DateTimeImmutable('now');

        return $base->add(new \DateInterval(self::TTL_DEFAULT));
    }

    /**
     * PG → PHP (ConverterJson), normalizado a mapa dl → fecha|null.
     *
     * @return array<string, string|null>
     */
    public static function fromPg(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        if (is_resource($raw)) {
            $raw = stream_get_contents($raw);
        }
        if (!is_string($raw) && !is_array($raw) && !($raw instanceof \stdClass)) {
            return [];
        }

        try {
            $decoded = (new ConverterJson($raw, true))->fromPg();
        } catch (JsonException) {
            return [];
        }

        return self::normalizarMapa(is_array($decoded) ? $decoded : []);
    }

    /**
     * PHP → PG (ConverterJson). null si el mapa está vacío.
     *
     * @param array<string, string|null> $mapa
     */
    public static function toPg(array $mapa): ?string
    {
        $mapa = self::normalizarMapa($mapa);
        if ($mapa === []) {
            return null;
        }
        ksort($mapa, SORT_NATURAL | SORT_FLAG_CASE);

        try {
            $json = (new ConverterJson($mapa, true))->toPg(false);
        } catch (JsonException) {
            return null;
        }

        return is_string($json) ? $json : null;
    }

    /**
     * Hidrata la columna en una fila de repositorio (mismo sitio que ConverterDate).
     *
     * @param array<string, mixed> $aDatos
     * @return array<string, mixed>
     */
    public static function hydrateRow(array $aDatos): array
    {
        if (!array_key_exists('publicado_para', $aDatos)) {
            return $aDatos;
        }
        $mapa = self::fromPg($aDatos['publicado_para']);
        $aDatos['publicado_para'] = $mapa === [] ? null : $mapa;

        return $aDatos;
    }

    /**
     * Alias de fromPg (compatibilidad).
     *
     * @return array<string, string|null>
     */
    public static function mapaFromJsonb(mixed $raw): array
    {
        return self::fromPg($raw);
    }

    /**
     * Alias de toPg (compatibilidad).
     *
     * @param array<string, string|null> $mapa
     */
    public static function mapaToJson(array $mapa): ?string
    {
        return self::toPg($mapa);
    }

    /**
     * @param array<int|string, mixed> $decoded
     * @return array<string, string|null>
     */
    private static function normalizarMapa(array $decoded): array
    {
        $mapa = [];
        if ($decoded !== [] && array_is_list($decoded)) {
            foreach ($decoded as $v) {
                if (!is_string($v) || $v === '') {
                    continue;
                }
                $mapa[self::normalizarDl($v)] = null;
            }

            return $mapa;
        }

        foreach ($decoded as $k => $v) {
            if (!is_string($k) || $k === '') {
                continue;
            }
            $dl = self::normalizarDl($k);
            if ($v === null || $v === '' || $v === 'null') {
                $mapa[$dl] = null;
                continue;
            }
            if ($v instanceof \DateTimeInterface) {
                $mapa[$dl] = \DateTimeImmutable::createFromInterface($v)->format('Y-m-d H:i:sP');
                continue;
            }
            if (is_string($v) || is_numeric($v)) {
                $mapa[$dl] = (string) $v;
            }
        }

        return $mapa;
    }

    /**
     * Une/actualiza un destino en el mapa. No acorta la caducidad de esa DL si ya era posterior.
     *
     * @param array<string, string|null> $mapa
     * @return array<string, string|null>
     */
    public static function mergeDestino(array $mapa, string $dl, ?\DateTimeInterface $hasta): array
    {
        $dl = self::normalizarDl($dl);
        if ($dl === '') {
            return $mapa;
        }

        if ($hasta === null) {
            $mapa[$dl] = null;

            return $mapa;
        }

        $nuevo = \DateTimeImmutable::createFromInterface($hasta);
        $nuevoIso = $nuevo->format('Y-m-d H:i:sP');

        if (!array_key_exists($dl, $mapa) || $mapa[$dl] === null || $mapa[$dl] === '') {
            $mapa[$dl] = $nuevoIso;

            return $mapa;
        }

        try {
            $actual = new \DateTimeImmutable((string) $mapa[$dl]);
            if ($actual > $nuevo) {
                return $mapa;
            }
        } catch (\Exception) {
            // ilegible → sobrescribir
        }

        $mapa[$dl] = $nuevoIso;

        return $mapa;
    }

    /**
     * @param array<string, string|null> $mapa
     * @return array<string, string|null>
     */
    public static function mapaVigente(array $mapa, ?\DateTimeInterface $ahora = null): array
    {
        $ahoraDt = $ahora !== null
            ? \DateTimeImmutable::createFromInterface($ahora)
            : new \DateTimeImmutable('now');

        $out = [];
        foreach ($mapa as $dl => $hasta) {
            if ($hasta === null || $hasta === '') {
                $out[$dl] = null;
                continue;
            }
            try {
                $f = new \DateTimeImmutable((string) $hasta);
                if ($f > $ahoraDt) {
                    $out[$dl] = $f->format('Y-m-d H:i:sP');
                }
            } catch (\Exception) {
                // fecha ilegible → no vigente
            }
        }

        return $out;
    }

    /**
     * Texto para listados: DLs vigentes (caducadas omitidas).
     */
    public static function textoVigentes(mixed $raw, ?\DateTimeInterface $ahora = null): string
    {
        $vigente = self::mapaVigente(self::fromPg($raw), $ahora);
        if ($vigente === []) {
            return '';
        }

        $parts = [];
        foreach ($vigente as $dl => $hasta) {
            if ($dl === self::DL_TODAS) {
                $parts[] = '*';
                continue;
            }
            if ($hasta === null || $hasta === '') {
                $parts[] = $dl;
                continue;
            }
            try {
                $parts[] = $dl . ' (' . (new \DateTimeImmutable($hasta))->format('d/m/Y') . ')';
            } catch (\Exception) {
                $parts[] = $dl;
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Expresión SQL: hay al menos un destino con vigencia (mapa objeto).
     */
    public static function sqlPublicacionVigente(string $alias = 'p'): string
    {
        $col = $alias === '' ? 'publicado_para' : $alias . '.publicado_para';

        return "($col IS NOT NULL"
            . " AND jsonb_typeof($col) = 'object'"
            . " AND EXISTS ("
            . " SELECT 1 FROM jsonb_each_text($col) AS e(k, v)"
            . " WHERE e.v IS NULL OR e.v::timestamptz > now()"
            . '))';
    }

    /**
     * Expresión SQL: vigente para una DL concreta o para "*".
     * Usa jsonb_exists() (no el operador `?`) para no chocar con placeholders PDO.
     */
    public static function sqlVigenteParaDl(string $quotedDl, string $quotedAll, string $col = 'publicado_para'): string
    {
        $vigenteKey = static fn(string $q): string => "("
            . "jsonb_exists($col, $q) AND ("
            . "$col->>$q IS NULL OR ($col->>$q)::timestamptz > now()"
            . '))';

        return '(' . $vigenteKey($quotedAll) . ' OR ' . $vigenteKey($quotedDl) . ')';
    }
}
