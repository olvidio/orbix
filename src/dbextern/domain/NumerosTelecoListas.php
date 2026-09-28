<?php

declare(strict_types=1);

namespace src\dbextern\domain;

/**
 * Parte un teléfono o un correo de la BDU cuando vienen varios valores
 * separados por coma, y decide qué registro de teleco reutilizar.
 */
final class NumerosTelecoListas
{
    /**
     * @return list<string>
     */
    public static function partes(string $texto): array
    {
        $trozos = preg_split('/\s*,\s*/u', trim($texto)) ?: [];
        $partes = [];
        $vistos = [];
        foreach ($trozos as $trozo) {
            if (!is_string($trozo)) {
                continue;
            }
            $trozo = trim($trozo);
            if ($trozo === '' || isset($vistos[$trozo])) {
                continue;
            }
            $vistos[$trozo] = true;
            $partes[] = $trozo;
        }

        return $partes;
    }

    /**
     * @param list<array{num: string, de_listas: bool}> $existentes
     * @param list<string> $deseados
     * @return list<array{op: 'update'|'create', index: int|null, num: string}>
     */
    public static function plan(array $existentes, array $deseados): array
    {
        $usados = [];
        $pasos = [];
        $pendientes = [];

        foreach ($deseados as $deseado) {
            $indice = self::indiceConNumero($existentes, $usados, $deseado);
            if ($indice !== null) {
                $usados[$indice] = true;
                $pasos[] = ['op' => 'update', 'index' => $indice, 'num' => $deseado];
            } else {
                $pendientes[] = $deseado;
            }
        }

        foreach ($pendientes as $posicion => $deseado) {
            $indice = self::indiceDeListasLibre($existentes, $usados);
            if ($indice === null && $usados === [] && $posicion === 0) {
                $indice = self::primerLibre($existentes, $usados);
            }
            if ($indice !== null) {
                $usados[$indice] = true;
                $pasos[] = ['op' => 'update', 'index' => $indice, 'num' => $deseado];
            } else {
                $pasos[] = ['op' => 'create', 'index' => null, 'num' => $deseado];
            }
        }

        return $pasos;
    }

    /**
     * @param list<array{num: string, de_listas: bool}> $existentes
     * @param array<int, true> $usados
     */
    private static function indiceConNumero(array $existentes, array $usados, string $numero): ?int
    {
        foreach ($existentes as $indice => $existente) {
            if (!isset($usados[$indice]) && $existente['num'] === $numero) {
                return $indice;
            }
        }

        return null;
    }

    /**
     * @param list<array{num: string, de_listas: bool}> $existentes
     * @param array<int, true> $usados
     */
    private static function indiceDeListasLibre(array $existentes, array $usados): ?int
    {
        foreach ($existentes as $indice => $existente) {
            if (!isset($usados[$indice]) && $existente['de_listas']) {
                return $indice;
            }
        }

        return null;
    }

    /**
     * @param list<array{num: string, de_listas: bool}> $existentes
     * @param array<int, true> $usados
     */
    private static function primerLibre(array $existentes, array $usados): ?int
    {
        foreach (array_keys($existentes) as $indice) {
            if (!isset($usados[$indice])) {
                return $indice;
            }
        }

        return null;
    }
}
