<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

enum OrderDirection: string
{
    case Asc = 'asc';
    case Desc = 'desc';
}
