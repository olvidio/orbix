<?php

declare(strict_types=1);

namespace frontend\devel_db_admin\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

final class DevelDbAdminPayload
{
    /**
     * @return array<int|string, string>
     */
    public static function desplegableOpciones(mixed $raw): array
    {
        return NotasFormSupport::desplegableOpciones($raw);
    }

    /**
     * @return list<string>
     */
    public static function avisosList(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            $out[] = PayloadCoercion::string($item);
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function migracionesSel(mixed $raw): array
    {
        if (!is_array($raw)) {
            if ($raw === null) {
                return [];
            }

            return self::migracionesSel([$raw]);
        }
        $out = [];
        foreach ($raw as $value) {
            $s = PayloadCoercion::string($value);
            if ($s !== '') {
                $out[] = $s;
            }
        }

        return $out;
    }

    public static function lineString(mixed $line): string
    {
        return PayloadCoercion::string($line);
    }
}
