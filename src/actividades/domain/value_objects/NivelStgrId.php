<?php

namespace src\actividades\domain\value_objects;

final class NivelStgrId
{

    public const B = 1; //Bienio
    public const C1 = 2; //	Cuadrienio Año I
    public const C2 = 3; //	Cuadrienio Año II-IV
    public const R = 4; //	Repaso
    public const CE = 5; //	centro estudios	ce
    public const NT = 6; //	Baja temporal
    public const X = 7; //	ap, pa, o ad
    public const N = 9; //	sin estudios
    public const E = 10; //	est. Ecles.
    public const BC = 11; //	bienio-cuadrienio


    public static function getArrayNivelStgrOn(): array
    {
        $a_status = [
            self::B => _("Bienio"),
            self::C1 => _("Cuadrienio Año I"),
            self::C2 => _("Cuadrienio Año II-IV"),
        ];

        return $a_status;
    }

    public static function getArrayNivelStgr(): array
    {
        $a_status = [
            self::B => _("Bienio"),
            self::C1 => _("Cuadrienio Año I"),
            self::C2 => _("Cuadrienio Año II-IV"),
            self::R => _("Repaso"),
            self::CE => _("centro estudios"),
            self::NT => _("Baja temporal"),
            self::X => _("ap, pa, o ad"),
            self::N => _("sin estudios"),
            self::E => _("est. Ecles."),
            self::BC => _("bienio-cuadrienio"),

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
        $aNivelStgr = self::getArrayNivelStgr();
        if (!array_key_exists($value, $aNivelStgr)) {
            throw new \InvalidArgumentException('NivelStgrId solo puede ser uno de los valores de la tabla');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(NivelStgrId $other): bool
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

    public static function generarNivelStgr($id_tipo_activ)
    {
        //según la tabla común: public.xa_nivel_stgr
        $nivel_stgr = '';
        switch ($id_tipo_activ) {
            case 112000: //bienio
            case 112020:
            case 133000:
            case 133020:
                $nivel_stgr = 1;
                break;
            case 112021: //cuadrienio
            case 112112: // semestre n
                $nivel_stgr = 2;
                break;
            case 133021:
                $nivel_stgr = 3;
                break;
            case 133105: // bienio y cuadrienio
                $nivel_stgr = 10;
                break;
            case 112023: //repaso
            case 133023:
            case 212023:
            case 233023:
                $nivel_stgr = 4;
                break;
            case 133016: // ceagd
                $nivel_stgr = 5;
                break;
        }
        return $nivel_stgr;
    }
}
