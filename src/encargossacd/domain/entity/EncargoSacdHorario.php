<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use web\NullTimeLocal;
use web\TimeLocal;

class EncargoSacdHorario
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_enc;

    private int $id_nom;

    private ?DateTimeLocal $f_ini = null;

    private ?DateTimeLocal $f_fin = null;

    private ?DiaRefCode $dia_ref = null;

    private ?int $dia_num = null;

    private ?MasMenosCode $mas_menos = null;

    private ?int $dia_inc = null;

    private ?TimeLocal $h_ini = null;

    private ?TimeLocal $h_fin = null;

    private ?int $id_item_tarea_sacd = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_enc(): int
    {
        return $this->id_enc;
    }


    public function setId_enc(int $id_enc): void
    {
        $this->id_enc = $id_enc;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
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
     * @deprecated usar getDiaRefVo()
     */
    public function getDia_ref(): ?string
    {
        return $this->dia_ref?->value();
    }

    /**
     * @deprecated usar setDiaRefVo()
     */
    public function setDia_ref(?string $dia_ref = null): void
    {
        $this->dia_ref = DiaRefCode::fromNullableString($dia_ref);
    }

    /**
     * @return DiaRefCode|null
     */
    public function getDiaRefVo(): ?DiaRefCode
    {
        return $this->dia_ref;
    }

    /**
     * @param DiaRefCode|null $vo
     */
    public function setDiaRefVo(DiaRefCode|string|null $vo = null): void
    {
        $this->dia_ref = $vo instanceof DiaRefCode
            ? $vo
            : DiaRefCode::fromNullableString($vo);
    }


    public function getDia_num(): ?int
    {
        return $this->dia_num;
    }


    public function setDia_num(?int $idia_num = null): void
    {
        $this->dia_num = $idia_num;
    }

    /**
     * @deprecated usar getMasMenosVo()
     */
    public function getMas_menos(): ?string
    {
        return $this->mas_menos?->value();
    }

    /**
     * @deprecated usar setMasMenosVo()
     */
    public function setMas_menos(?string $mas_menos = null): void
    {
        $this->mas_menos = MasMenosCode::fromNullableString($mas_menos);
    }

    /**
     * @return MasMenosCode|null
     */
    public function getMasMenosVo(): ?MasMenosCode
    {
        return $this->mas_menos;
    }

    /**
     * @param MasMenosCode|null $vo
     */
    public function setMasMenosVo(MasMenosCode|string|null $vo = null): void
    {
        $this->mas_menos = $vo instanceof MasMenosCode
            ? $vo
            : MasMenosCode::fromNullableString($vo);
    }

    public function getDia_inc(): ?int
    {
        return $this->dia_inc;
    }


    public function setDia_inc(?int $idia_inc = null): void
    {
        $this->dia_inc = $idia_inc;
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


    public function getId_item_tarea_sacd(): ?int
    {
        return $this->id_item_tarea_sacd;
    }


    public function setId_item_tarea_sacd(?int $id_item_tarea_sacd = null): void
    {
        $this->id_item_tarea_sacd = $id_item_tarea_sacd;
    }
}