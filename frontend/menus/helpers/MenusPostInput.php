<?php

declare(strict_types=1);

namespace frontend\menus\helpers;

final class MenusPostInput
{
    public static function selFirstItem(mixed $aSel): mixed
    {
        if (!is_array($aSel)) {
            return null;
        }
        foreach ($aSel as $item) {
            return $item;
        }

        return null;
    }

    public static function idFromSelItem(mixed $sel0): int
    {
        if (!is_string($sel0) || $sel0 === '') {
            return 0;
        }
        $parts = explode('#', $sel0, 2);
        $idRaw = $parts[0];

        return is_numeric($idRaw) ? (int) $idRaw : 0;
    }
}
