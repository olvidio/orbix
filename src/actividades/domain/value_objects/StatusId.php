<?php

namespace src\actividades\domain\value_objects;

final class StatusId
{
    public const PROYECTO = 1;
    public const ACTUAL = 2;
    public const TERMINADA = 3;
    public const BORRABLE = 4;
    public const ALL = 9;


    public static function getArrayStatus(bool $includeAll = true): array
    {
        $a_status = [
            self::PROYECTO => _("proyecto"),
            self::ACTUAL => _("actual"),
            self::TERMINADA => _("terminada"),
            self::BORRABLE => _("borrable"),
        ];

        if ($includeAll) {
            $a_status[self::ALL] = _("cualquiera");
        }

        return $a_status;
    }

    // ---------------------------------------------------------------------------
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!in_array($value, [self::PROYECTO, self::ACTUAL, self::TERMINADA, self::BORRABLE], true)) {
            throw new \InvalidArgumentException('StatusId solo puede ser 1, 2, 3 o 4');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(StatusId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
