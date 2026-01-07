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

    private ?PlazasNumero $plazas = null;

    private ?PlazaClCode $cl = null;

    private DelegacionTablaCode $dl_tabla;

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
        return $this->plazas;
    }

    /**
     * @param PlazasNumero|null $oPlazasNumero
     */
    public function setPlazasVo(PlazasNumero|int|null $valor = null): void
    {
        $this->plazas = $valor instanceof PlazasNumero
            ? $valor
            : PlazasNumero::fromNullable($valor);
    }

    /**
     * @deprecated use getPlazasVo()
     */
    public function getPlazas(): ?string
    {
        return $this->plazas?->value();
    }

    /**
     * @deprecated use setPlazasVo()
     */
    public function setPlazas(?int $plazas = null): void
    {
        $this->plazas = PlazasNumero::fromNullable($plazas);
    }

    /**
     * @return PlazaClCode|null
     */
    public function getClVo(): ?PlazaClCode
    {
        return $this->cl;
    }

    /**
     * @param PlazaClCode|null $oPlazaClCode
     */
    public function setClVo(PlazaClCode|string|null $texto = null): void
    {
        $this->cl = $texto instanceof PlazaClCode
            ? $texto
            : PlazaClCode::fromNullableString($texto);
    }

    /**
     *
     * @deprecated use getClVo()
     */
    public function getCl(): ?string
    {
        return $this->cl?->value();
    }

    /**
     *
     * @deprecated use setClVo()
     */
    public function setCl(?string $cl = null): void
    {
        $this->cl = PlazaClCode::fromNullableString($cl);
    }

    /**
     * @return DelegacionTablaCode
     */
    public function getDlTablaVo(): DelegacionTablaCode
    {
        return $this->dl_tabla;
    }

    /**
     * @param DelegacionTablaCode $oDelegacionTablaCode
     */
    public function setDlTablaVo(DelegacionTablaCode|string $texto): void
    {
        $this->dl_tabla = $texto instanceof DelegacionTablaCode
            ? $texto
            : DelegacionTablaCode::fromNullableString($texto);
    }

    /**
     * @deprecated use getDlTablaVo()
     */
    public function getDl_tabla(): string
    {
        return $this->dl_tabla->value();
    }

    /**
     * @deprecated use setDlTablaVo()
     */
    public function setDlTabla(string $dl_tabla): void
    {
        $this->dl_tabla = DelegacionTablaCode::fromNullableString($dl_tabla);
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