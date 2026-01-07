<?php

namespace src\dossiers\domain\entity;

use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

class Dossier
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private string $tabla;

    private int $id_pau;

    private int $id_tipo_dossier;

   private ?DateTimeLocal $f_ini = null;

   private ?DateTimeLocal $f_camb_dossier = null;

    private ?bool $active;

   private ?DateTimeLocal $f_active = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function abrir(): void
    {
        $oHoy = new DateTimeLocal();
        $this->setF_active($oHoy);
        $this->setActive('t');
    }

    public function cerrar(): void
    {
        $oHoy = new DateTimeLocal();
        $this->setF_active($oHoy);
        $this->setActive('f');
    }

    public function getTabla(): string
    {
        return $this->tabla;
    }


    public function setTabla(string $tabla): void
    {
        $this->tabla = $tabla;
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


    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }


    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }


    public function getF_camb_dossier(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_camb_dossier ?? new NullDateTimeLocal;
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


    public function getF_active(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_active ?? new NullDateTimeLocal;
    }


    public function setF_active(DateTimeLocal|null $f_active = null): void
    {
        $this->f_active = $f_active;
    }
}