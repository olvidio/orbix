<?php

namespace src\actividadplazas\domain\entity;

use src\actividadplazas\domain\value_objects\DelegacionTablaCode;
use src\actividadplazas\domain\value_objects\PlazaClCode;
use src\actividadplazas\domain\value_objects\PlazasNumero;
use src\shared\domain\traits\Hydratable;
use src\shared\traits\HandlesPdoErrors;
use stdClass;

class ActividadPlazas
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_activ;

    private int $id_dl;

    private int|null $plazas = null;

    private string|null $cl = null;

    private string $dl_tabla;

    private array|stdClass|null $cedidas = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_activ(): int
    {
        return $this->id_activ;
    }


    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }


    public function getId_dl(): int
    {
        return $this->id_dl;
    }


    public function setId_dl(int $id_dl): void
    {
        $this->id_dl = $id_dl;
    }

    /**
     * @return PlazasNumero|null
     */
    public function getPlazasVo(): ?PlazasNumero
    {
        return $this->plazas !== null ? new PlazasNumero($this->plazas) : null;
    }

    /**
     * @param PlazasNumero|null $oPlazasNumero
     */
    public function setPlazasVo(?PlazasNumero $oPlazasNumero = null): void
    {
        $this->plazas = $oPlazasNumero?->value();
    }

    /**
     * @deprecated use getPlazasVo()
     */
    public function getPlazas(): ?int
    {
        return $this->plazas;
    }

    /**
     * @deprecated use setPlazasVo()
     */
    public function setPlazas(?int $plazas = null): void
    {
        $this->plazas = $plazas;
    }

    /**
     * @return PlazaClCode|null
     */
    public function getClVo(): ?PlazaClCode
    {
        return $this->cl !== null ? new PlazaClCode($this->cl) : null;
    }

    /**
     * @param PlazaClCode|null $oPlazaClCode
     */
    public function setClVo(?PlazaClCode $oPlazaClCode = null): void
    {
        $this->cl = $oPlazaClCode?->value();
    }

    /**
     *
     * @deprecated use getClVo()
     */
    public function getCl(): ?string
    {
        return $this->cl;
    }

    /**
     *
     * @deprecated use setClVo()
     */
    public function setCl(?string $cl = null): void
    {
        $this->cl = $cl;
    }

    /**
     * @return DelegacionTablaCode
     */
    public function getDlTablaVo(): DelegacionTablaCode
    {
        return new DelegacionTablaCode($this->dl_tabla);
    }

    /**
     * @param DelegacionTablaCode $oDelegacionTablaCode
     */
    public function setDlTablaVo(DelegacionTablaCode $oDelegacionTablaCode): void
    {
        $this->dl_tabla = $oDelegacionTablaCode->value();
    }

    /**
     * @deprecated use getDlTablaVo()
     */
    public function getDl_tabla(): string
    {
        return $this->dl_tabla;
    }

    /**
     * @deprecated use setDlTablaVo()
     */
    public function setDlTabla(string $dl_tabla): void
    {
        $this->dl_tabla = $dl_tabla;
    }

    /**
     *
     * @return array|stdClass|null $cedidas
     */
    public function getCedidas(): array|stdClass|null
    {
        return $this->cedidas;
    }

    /**
     *
     * @param stdClass|array|null $cedidas
     */
    public function setCedidas(stdClass|array|null $cedidas = null): void
    {
        $this->cedidas = $cedidas;
    }
}