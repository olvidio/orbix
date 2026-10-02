<?php

namespace src\misas\domain\value_objects;

final class EncargoDiaStatus
{
    public const STATUS_PROPUESTA = 1;
    public const STATUS_COMUNICADO_SACD = 2;
    public const STATUS_COMUNICADO_CTR = 3;

    /**
     * @return array<int, string>
     */
    public static function getArrayStatus(): array
    {
        return [
            self::STATUS_PROPUESTA => _('propuesta'),
            self::STATUS_COMUNICADO_SACD => _('visible sacd'),
            self::STATUS_COMUNICADO_CTR => _('visible ctr'),
        ];
    }

    /**
     * Estado a persistir cuando la celda no trae uno válido. Por defecto, propuesta.
     */
    public static function valueOrPropuesta(?int $status): int
    {
        if ($status !== null && array_key_exists($status, self::getArrayStatus())) {
            return $status;
        }

        return self::STATUS_PROPUESTA;
    }

    /** Celda del plan que no se borra al pulsar «preparar» en nuevo plan. */
    public static function preserveWhenPreparingPlan(?int $status): bool
    {
        return $status === self::STATUS_COMUNICADO_CTR;
    }

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!array_key_exists($value, self::getArrayStatus())) {
            throw new \InvalidArgumentException(
                sprintf('Invalid status value: %d. Valid values are: %s',
                    $value,
                    implode(', ', array_keys(self::getArrayStatus()))
                )
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function getDescripcion(): string
    {
        return self::getArrayStatus()[$this->value];
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
