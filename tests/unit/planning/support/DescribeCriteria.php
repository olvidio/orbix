<?php

declare(strict_types=1);

namespace Tests\unit\planning\support;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Describe un Criteria como lista legible para aserciones: ["campo operador valor", …] y el orden.
 */
final class DescribeCriteria
{
    /**
     * @return list<string>
     */
    public static function filtros(Criteria $c): array
    {
        $out = [];
        foreach ($c->filters() as $f) {
            if ($f instanceof Filter) {
                $v = $f->value;
                $out[] = $f->field->value . ' ' . $f->operator->value
                    . ($v === null ? '' : ' ' . (is_array($v) ? '[' . implode(',', $v) . ']' : (is_bool($v) ? ($v ? 'true' : 'false') : (string) $v)));
            } else {
                $out[] = '(grupo)';
            }
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function orden(Criteria $c): array
    {
        $out = [];
        foreach ($c->orders() as $o) {
            $out[] = $o->field->value . ' ' . $o->direction->value;
        }

        return $out;
    }
}
