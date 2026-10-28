<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

enum LogicalOperator: string
{
    case And = 'and';
    case Or = 'or';
}
