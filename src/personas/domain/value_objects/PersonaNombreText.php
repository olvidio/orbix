<?php

namespace src\personas\domain\value_objects;

final class PersonaNombreText
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
            throw new \InvalidArgumentException('PersonaNombreText cannot be empty');
        }
        $len = mb_strlen($value);
        if ($len > 40) {
            throw new \InvalidArgumentException(sprintf(
                'PersonaNombreText must be at most 40 characters (length=%d, value=%s)',
                $len,
                self::safeRepr($value)
            ));
        }
        if (!preg_match("/^[\p{L}0-9 .,'’´\-()?]+$/u", $value)) {
            throw new \InvalidArgumentException(sprintf(
                'PersonaNombreText has invalid characters (value=%s; no permitidos: %s)',
                self::safeRepr($value),
                self::disallowedCharsSummary($value)
            ));
        }
    }

    /**
     * Texto seguro para mensajes de error (JSON UTF-8, truncado).
     */
    private static function safeRepr(string $value): string
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

    /**
     * Lista de caracteres que no cumplen el patrón permitido (carácter + código Unicode).
     *
     * @return non-empty-string
     */
    private static function disallowedCharsSummary(string $value): string
    {
        $seen = [];
        $parts = [];
        $len = mb_strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($value, $i, 1);
            if (preg_match("/^[\p{L}0-9 .,'’´\-()]$/u", $ch)) {
                continue;
            }
            $ord = mb_ord($ch, 'UTF-8');
            $key = $ch . "\0" . (string)$ord;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $encFlags = JSON_UNESCAPED_UNICODE;
            if (defined('JSON_INVALID_SUBSTITUTE')) {
                $encFlags |= JSON_INVALID_SUBSTITUTE;
            }
            $enc = json_encode($ch, $encFlags);
            $parts[] = sprintf('%s (U+%04X)', is_string($enc) ? $enc : '?', $ord >= 0 ? $ord : 0);
        }

        return $parts !== [] ? implode(', ', $parts) : '(sin detalle)';
    }

    public function value(): string { return $this->value; }
    public function __toString(): string { return $this->value; }

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
