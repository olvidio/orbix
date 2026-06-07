<?php

declare(strict_types=1);

namespace src\misas\application\support;

use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

final class EncargoDiaTimeHelper
{
    public static function hora(DateTimeLocal|NullDateTimeLocal $dt): string
    {
        if ($dt instanceof DateTimeLocal) {
            return $dt->getHora() ?? '';
        }

        return '';
    }

    public static function format(DateTimeLocal|NullDateTimeLocal $dt, string $format): string
    {
        if ($dt instanceof DateTimeLocal) {
            return $dt->format($format);
        }

        return '';
    }
}
