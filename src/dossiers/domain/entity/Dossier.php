<?php

namespace src\dossiers\domain\entity;

use src\dossiers\domain\value_objects\DossierPk;
use src\dossiers\domain\value_objects\DossierTabla;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;

class Dossier
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private DossierTabla $tabla;

    private int $id_pau;

    private int $id_tipo_dossier;

    private ?DateTimeLocal $f_ini = null;

    private ?DateTimeLocal $f_camb_dossier = null;

    private bool $active = false;

    private ?DateTimeLocal $f_active = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getDossierPk(): DossierPk
    {
        return DossierPk::fromArray(['id_tipo_dossier' => $this->id_tipo_dossier,
            'id_pau' => $this->id_pau,
            'tabla' => $this->tabla
        ]);
    }

    public function abrir(): void
    {
        $oHoy = new DateTimeLocal();
        $this->setF_active($oHoy);
        $this->setActive(true);
    }

    public function cerrar(): void
    {
        $oHoy = new DateTimeLocal();
        $this->setF_active($oHoy);
        $this->setActive(false);
    }

    /**
     * @deprecated use getTablaVo()
     */
    public function getTabla(): string
    {
        return $this->tabla->value();
    }

    public function getTablaVo(): DossierTabla
    {
        return $this->tabla;
    }

    /**
     * @deprecated use setTablaVo()
     */
    public function setTabla(string $tabla): void
    {
        $vo = DossierTabla::fromNullableString($tabla);
        if ($vo === null) {
            throw new \InvalidArgumentException('tabla cannot be empty');
        }
        $this->tabla = $vo;
    }

    public function setTablaVo(DossierTabla|string|null $vo): void
    {
        $this->tabla = $vo instanceof DossierTabla
            ? $vo
            : (DossierTabla::fromNullableString(is_string($vo) ? $vo : null) ?? throw new \InvalidArgumentException('tabla cannot be empty'));
    }


    public function getId_pau(): int
    {
        return $this->id_pau;
    }


    public function setId_pau(int $id_pau): void
    {
        $this->id_pau = $id_pau;
    }


    public function getId_tipo_dossier(): int
    {
        return $this->id_tipo_dossier;
    }


    public function setId_tipo_dossier(int $id_tipo_dossier): void
    {
        $this->id_tipo_dossier = $id_tipo_dossier;
    }


    public function getF_ini(): DateTimeLocal|null
    {
        return $this->f_ini;
    }


    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }


    public function getF_camb_dossier(): DateTimeLocal|null
    {
        return $this->f_camb_dossier;
    }


    public function setF_camb_dossier(DateTimeLocal|null $f_camb_dossier = null): void
    {
        $this->f_camb_dossier = $f_camb_dossier;
    }


    public function isActive(): bool
    {
        return $this->active;
    }


    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    public function getF_active(): DateTimeLocal|null
    {
        return $this->f_active;
    }


    public function setF_active(DateTimeLocal|null $f_active = null): void
    {
        $this->f_active = $f_active;
    }

}