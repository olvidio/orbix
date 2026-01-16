<?php

namespace src\misas\domain\value_objects;

final class EncargoDiaStatus
{
    public const STATUS_PROPUESTA = 1;
    public const STATUS_COMUNICADO_SACD = 2;
    public const STATUS_COMUNICADO_CTR = 3;

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private static function validStatuses(): array
    {
        return [
            self::STATUS_PROPUESTA,
            self::STATUS_COMUNICADO_SACD,
            self::STATUS_COMUNICADO_CTR,
        ];
    }

    private function validate(int $value): void
    {
        if (!in_array($value, self::validStatuses(), true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid status value: %d. Valid values are: %s',
                    $value,
                    implode(', ', self::validStatuses())
                )
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(EncargoDiaStatus $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }

    public static function propuesta(): self
    {
        return new self(self::STATUS_PROPUESTA);
    }

    public static function comunicadoSacd(): self
    {
        return new self(self::STATUS_COMUNICADO_SACD);
    }

    public static function comunicadoCtr(): self
    {
        return new self(self::STATUS_COMUNICADO_CTR);
    }

    public function isPropuesta(): bool
    {
        return $this->value === self::STATUS_PROPUESTA;
    }

    public function isComunicadoSacd(): bool
    {
        return $this->value === self::STATUS_COMUNICADO_SACD;
    }

    public function isComunicadoCtr(): bool
    {
        return $this->value === self::STATUS_COMUNICADO_CTR;
    }
}
