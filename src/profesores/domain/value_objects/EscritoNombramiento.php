<?php

namespace src\profesores\domain\value_objects;

final class EscritoNombramiento
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('EscritoNombramiento cannot be empty');
        }
        $len = mb_strlen($value);
        if ($len > 30) {
            throw new \InvalidArgumentException(sprintf(
                'EscritoNombramiento must be at most 30 characters (length=%d, value=%s)',
                $len,
                self::reprForException($value)
            ));
        }
    }

    private static function reprForException(string $value): string
    {
        $flags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_SUBSTITUTE')) {
            $flags |= JSON_INVALID_SUBSTITUTE;
        }
        $json = json_encode($value, $flags);
        if (!is_string($json)) {
            return '(valor no codificable)';
        }
        if (strlen($json) > 160) {
            return substr($json, 0, 157) . '...';
        }

        return $json;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        return new self($value);
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
