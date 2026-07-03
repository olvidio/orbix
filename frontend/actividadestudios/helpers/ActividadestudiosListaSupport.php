<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;

final class ActividadestudiosListaSupport
{
    /**
     * @return array<int|string, mixed>
     */
    public static function valores(mixed $raw, mixed $select = null, mixed $scrollId = null): array
    {
        $valores = ActividadesListaSupport::datos($raw);
        $selectStr = \frontend\shared\helpers\PayloadCoercion::string($select);
        if ($selectStr !== '') {
            $valores['select'] = $selectStr;
        }
        $scrollStr = \frontend\shared\helpers\PayloadCoercion::string($scrollId);
        if ($scrollStr !== '') {
            $valores['scroll_id'] = $scrollStr;
        }

        return $valores;
    }
}
