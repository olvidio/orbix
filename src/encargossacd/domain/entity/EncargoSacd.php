<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\EncargoModoId;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;


class EncargoSacd
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_enc;

    private int $id_nom;

    private EncargoModoId $modo;

    private DateTimeLocal $f_ini;

   private ?DateTimeLocal $f_fin = null;

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

    /**
     * @deprecated Usar `getModoVo(): EncargoModoId` en su lugar.
     */
    public function getModo(): int
    {
        return $this->modo->value();
    }

    /**
     * @deprecated Usar `setModoVo(EncargoModoId $vo): void` en su lugar.
     */
    public function setModo(int $modo): void
    {
        $this->modo = EncargoModoId::fromNullableInt($modo);
    }

    public function getModoVo(): EncargoModoId
    {
        return $this->modo;
    }

    public function setModoVo(EncargoModoId|int|null $vo): void
    {
        $this->modo = $vo instanceof EncargoModoId
            ? $vo
            : EncargoModoId::fromNullableInt($vo);
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
}