<?php

namespace src\actividadplazas\domain\entity;

use src\actividadplazas\domain\value_objects\DelegacionTablaCode;
use src\actividadplazas\domain\value_objects\PlazaClCode;
use src\actividadplazas\domain\value_objects\PlazasNumero;
use src\shared\domain\traits\Hydratable;
use src\shared\traits\HandlesPdoErrors;

class ActividadPlazas
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_activ;

    private int $id_dl;

    private ?PlazasNumero $plazas = null;

    private ?PlazaClCode $cl = null;

    private DelegacionTablaCode $dl_tabla;

    /** @var array<string, int>|null */
    private ?array $cedidas = null;

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

    public function setPlazasVo(PlazasNumero|int|null $valor = null): void
    {
        $this->plazas = $valor instanceof PlazasNumero
            ? $valor
            : PlazasNumero::fromNullableInt($valor);
    }

    /**
     * @deprecated use getPlazasVo()
     */
    public function getPlazas(): ?string
    {
        $value = $this->plazas?->value();

        return $value !== null ? (string)$value : null;
    }

    /**
     * @deprecated use setPlazasVo()
     */
    public function setPlazas(?int $plazas = null): void
    {
        $this->plazas = PlazasNumero::fromNullableInt($plazas);
    }

    /**
     * @return PlazaClCode|null
     */
    public function getClVo(): ?PlazaClCode
    {
        return $this->cl;
    }

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

    public function setDlTablaVo(DelegacionTablaCode|string $texto): void
    {
        if ($texto instanceof DelegacionTablaCode) {
            $this->dl_tabla = $texto;
            return;
        }
        $vo = DelegacionTablaCode::fromNullableString($texto);
        if ($vo === null) {
            throw new \InvalidArgumentException('DelegacionTablaCode no puede estar vacío');
        }
        $this->dl_tabla = $vo;
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
        $this->setDlTablaVo($dl_tabla);
    }

    /**
     * @return array<string, int>|null
     */
    public function getArrayCedidas(): ?array
    {
        return $this->cedidas;
    }

    /**
     * @param array<string, int>|null $cedidas
     */
    public function setCedidas(?array $cedidas = null): void
    {
        $this->cedidas = $cedidas;
    }
}
