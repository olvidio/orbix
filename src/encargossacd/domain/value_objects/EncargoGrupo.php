<?php

namespace src\encargossacd\domain\value_objects;

class EncargoGrupo
{

    public const CENTRO_SV = 1;
    public const CENTRO_SF = 2;
    public const CENTRO_SSSC = 3;
    public const IGL = 4;
    public const CGI = 5;
    public const ZONAS_MISAS = 8;


    public static function getArrayGrupos(): array
    {
        $a_status = [
            self::CENTRO_SV => _("centro sv"),
            self::CENTRO_SF => _("centro sf"),
            self::CENTRO_SSSC => _("centro sss+"),
            self::IGL => _("iglesias"),
            self::CGI => _("colegios"),
            self::ZONAS_MISAS => _("zonas misas"),
        ];

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
        if (!array_key_exists($value, self::getArrayGrupos())) {
            throw new \InvalidArgumentException('EncargoGrupo solo admite: 1, 2, 3, 4, 5, 8');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(EncargoGrupo $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}