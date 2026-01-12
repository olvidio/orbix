<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\encargossacd\domain\value_objects\MesNum;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use web\NullTimeLocal;
use web\TimeLocal;


class EncargoHorario
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_enc;

    private int $id_item_h;

    private DateTimeLocal $f_ini;

    private ?DateTimeLocal $f_fin = null;

    private ?DiaRefCode $dia_ref = null;

    private ?int $dia_num = null;

    private ?MasMenosCode $mas_menos = null;

    private ?int $dia_inc = null;

    private ?TimeLocal $h_ini = null;

    private ?TimeLocal $h_fin = null;

    private ?int $n_sacd = null;

    private ?MesNum $mes = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_enc(): int
    {
        return $this->id_enc;
    }


    public function setId_enc(int $id_enc): void
    {
        $this->id_enc = $id_enc;
    }


    public function getId_item_h(): int
    {
        return $this->id_item_h;
    }


    public function setId_item_h(int $id_item_h): void
    {
        $this->id_item_h = $id_item_h;
    }


    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }


    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }


    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_fin ?? new NullDateTimeLocal;
    }


    public function setF_fin(DateTimeLocal|null $f_fin = null): void
    {
        $this->f_fin = $f_fin;
    }

    /**
     * @deprecated Usar `getDiaRefVo(): ?DiaRefCode` en su lugar.
     */
    public function getDia_ref(): ?string
    {
        return $this->dia_ref?->value();
    }

    /**
     * @deprecated Usar `setDiaRefVo(?DiaRefCode $vo): void` en su lugar.
     */
    public function setDia_ref(?string $dia_ref = null): void
    {
        $this->dia_ref = DiaRefCode::fromNullableString($dia_ref);
    }

    public function getDiaRefVo(): ?DiaRefCode
    {
        return $this->dia_ref;
    }

    public function setDiaRefVo(DiaRefCode|string|null $vo): void
    {
        $this->dia_ref = $vo instanceof DiaRefCode
            ? $vo
            : DiaRefCode::fromNullableString($vo);
    }


    public function getDia_num(): ?int
    {
        return $this->dia_num;
    }


    public function setDia_num(?int $dia_num = null): void
    {
        $this->dia_num = $dia_num;
    }

    /**
     * @deprecated Usar `getMasMenosVo(): ?MasMenosCode` en su lugar.
     */
    public function getMas_menos(): ?string
    {
        return $this->mas_menos?->value();
    }

    /**
     * @deprecated Usar `setMasMenosVo(?MasMenosCode $vo): void` en su lugar.
     */
    public function setMas_menos(?string $mas_menos = null): void
    {
        $this->mas_menos = MasMenosCode::fromNullableString($mas_menos);
    }

    public function getMasMenosVo(): ?MasMenosCode
    {
        return $this->mas_menos;
    }

    public function setMasMenosVo(MasMenosCode|string|null $vo): void
    {
        $this->mas_menos = $vo instanceof MasMenosCode
            ? $vo
            : MasMenosCode::fromNullableString($vo);
    }


    public function getDia_inc(): ?int
    {
        return $this->dia_inc;
    }


    public function setDia_inc(?int $dia_inc = null): void
    {
        $this->dia_inc = $dia_inc;
    }


    public function getH_ini(): TimeLocal|NullTimeLocal|null
    {
        return $this->h_ini;
    }


    public function setH_ini(TimeLocal|null $h_ini = null): void
    {
        $this->h_ini = $h_ini;
    }


    public function getH_fin(): TimeLocal|NullTimeLocal|null
    {
        return $this->h_fin;
    }


    public function setH_fin(TimeLocal|null $h_fin = null): void
    {
        $this->h_fin = $h_fin;
    }


    public function getN_sacd(): ?int
    {
        return $this->n_sacd;
    }


    public function setN_sacd(?int $n_sacd = null): void
    {
        $this->n_sacd = $n_sacd;
    }

    /**
     * @deprecated Usar `getMesVo(): ?MesNum` en su lugar.
     */
    public function getMes(): ?int
    {
        return $this->mes?->value();
    }

    /**
     * @deprecated Usar `setMesVo(?MesNum $vo): void` en su lugar.
     */
    public function setMes(?int $mes = null): void
    {
        $this->mes = MesNum::fromNullableInt($mes);
    }

    public function getMesVo(): ?MesNum
    {
        return $this->mes;
    }

    public function setMesVo(MesNum|int|null $vo): void
    {
        $this->mes = $vo instanceof MesNum
            ? $vo
            : MesNum::fromNullableInt($vo);
    }
}