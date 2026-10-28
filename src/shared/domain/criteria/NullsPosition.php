<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

/** Posición de los nulos en una ordenación (`NULLS FIRST` / `NULLS LAST`). */
enum NullsPosition: string
{
    case First = 'first';
    case Last = 'last';
}
