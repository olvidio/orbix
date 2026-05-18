<?php

namespace src\personas\domain\value_objects;

final class ApelFamText
{
    /** Mismo criterio que {@see PersonaApellido2Text} y {@see PersonaNombreText}. */
    private const ALLOWED_PATTERN = '/^[\p{L}0-9 .,\'’´\-()?·]+$/u';

    private string $value;

    public function __construct(string $value)
    {
        $value = self::normalize(trim($value));
        $this->validate($value);
        $this->value = $value;
    }

    private static function normalize(string $value): string
    {
        return str_replace(
            [
                "\u{2013}", "\u{2014}", "\u{2212}",
                "\u{2018}", "\u{2019}", "\u{201B}",
                '`',
            ],
            ['-', '-', '-', "'", "'", "'", "'"],
            $value
        );
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('ApelFamText cannot be empty');
        }
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('ApelFamText must be at most 20 characters');
        }
        if (!preg_match(self::ALLOWED_PATTERN, $value)) {
            throw new \InvalidArgumentException(sprintf(
                'ApelFamText has invalid characters (value=%s; no permitidos: %s)',
                self::safeRepr($value),
                self::disallowedCharsSummary($value)
            ));
        }
    }

    private static function safeRepr(string $value): string
    {
        $flags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_SUBSTITUTE')) {
            $flags |= JSON_INVALID_SUBSTITUTE;
        }
        $json = json_encode($value, $flags);

        return is_string($json) ? $json : '(valor no codificable)';
    }

    /**
     * @return non-empty-string
     */
    private static function disallowedCharsSummary(string $value): string
    {
        $seen = [];
        $parts = [];
        $len = mb_strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($value, $i, 1);
            if (preg_match(self::ALLOWED_PATTERN, $ch)) {
                continue;
            }
            $ord = mb_ord($ch, 'UTF-8');
            $key = $ch . "\0" . (string)$ord;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $enc = json_encode($ch, JSON_UNESCAPED_UNICODE);
            $parts[] = sprintf('%s (U+%04X)', is_string($enc) ? $enc : '?', $ord >= 0 ? $ord : 0);
        }

        return $parts !== [] ? implode(', ', $parts) : '(sin detalle)';
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
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
