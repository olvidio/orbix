<?php

namespace src\actividades\domain\value_objects;

final class NivelStgrId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('NivelStgrId must be a positive integer');
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
        if ($value === null) { return null; }
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
