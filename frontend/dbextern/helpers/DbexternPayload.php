<?php

declare(strict_types=1);

namespace frontend\dbextern\helpers;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFrontSignedLink;

final class DbexternPayload
{
    public static function signedLink(mixed $spec): string
    {
        return HashFrontSignedLink::tryFromSpec($spec);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function sessionDbListas(): array
    {
        $raw = $_SESSION['DBListas'] ?? null;
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            if (is_int($key)) {
                $out[$key] = $row;
                continue;
            }
            if (is_numeric($key)) {
                $out[(int) $key] = $row;
            }
        }

        return $out;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function sessionDbOrbix(): array
    {
        $raw = $_SESSION['DBOrbix'] ?? null;
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            if (is_int($key)) {
                $out[$key] = $row;
                continue;
            }
            if (is_numeric($key)) {
                $out[(int) $key] = $row;
            }
        }

        return $out;
    }

    public static function otroListas(int $id, string $mov, int $max): int
    {
        $listas = self::sessionDbListas();
        switch ($mov) {
            case '-':
                $id--;
                if ($id < 1) {
                    return 1;
                }
                break;
            case '+':
                $id++;
                if ($id > $max) {
                    return $max;
                }
                break;
            default:
                $id = 1;
        }
        if (isset($listas[$id])) {
            return $id;
        }

        return self::otroListas($id, $mov, $max);
    }

    /**
     * @return int|false
     */
    public static function otroOrbix(int $id, string $mov, int $max): int|false
    {
        $orbix = self::sessionDbOrbix();
        if ($max === 0) {
            return false;
        }
        switch ($mov) {
            case '-':
                $id--;
                if ($id < 1) {
                    return 1;
                }
                break;
            case '+':
                $id++;
                if ($id > $max) {
                    return $max;
                }
                break;
            default:
                $id = 1;
        }
        if (isset($orbix[$id])) {
            return $id;
        }

        return self::otroOrbix($id, $mov, $max);
    }

    /**
     * @return array{id_nom_listas: string}
     */
    public static function personaListasRow(mixed $raw): array
    {
        if (!is_array($raw)) {
            return ['id_nom_listas' => ''];
        }

        return [
            'id_nom_listas' => \frontend\shared\helpers\PayloadCoercion::string($raw['id_nom_listas'] ?? ''),
        ];
    }

    /**
     * @return array{id_nom_orbix: string}
     */
    public static function personaOrbixRow(mixed $raw): array
    {
        if (!is_array($raw)) {
            return ['id_nom_orbix' => ''];
        }

        return [
            'id_nom_orbix' => \frontend\shared\helpers\PayloadCoercion::string($raw['id_nom_orbix'] ?? ''),
        ];
    }

    /**
     * @param array<int|string, mixed> $matches
     * @return array<int|string, mixed>
     */
    public static function listaBduFromMatches(array $matches): array
    {
        return $matches;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listaFromBackend(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            if (is_int($key)) {
                $out[$key] = self::rowStringKeys($row);
                continue;
            }
            if (is_numeric($key)) {
                $out[(int) $key] = self::rowStringKeys($row);
            }
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $row
     * @return array<string, mixed>
     */
    public static function rowStringKeys(array $row): array
    {
        $out = [];
        foreach ($row as $key => $value) {
            if (is_int($key)) {
                continue;
            }
            $out[$key] = $value;
        }

        return $out;
    }
}
