<?php

namespace src\misas\domain\entity;

use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Clase que implementa la entidad misa_cuadricula_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
class EncargoDia
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private EncargoDiaId $uuid_item;
    private int $id_enc;

    private DateTimeLocal|NullDateTimeLocal|null $tstart;
    private DateTimeLocal|NullDateTimeLocal|null $tend;

    private ?int $id_nom = null;
    private ?string $observ = null;
    private ?EncargoDiaStatus $status = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated use getUuidItemVo()
     */
    public function getUuid_item(): string
    {
        return $this->uuid_item->value();
    }

    public function getUuidItemVo(): EncargoDiaId
    {
        return $this->uuid_item;
    }

    /**
     * @deprecated use setUuidItemVo()
     */
    public function setUuid_item(string $uuid_item): void
    {
        $this->uuid_item = new EncargoDiaId($uuid_item);
    }
    public function setUuidItemVo(EncargoDiaId|string $uuid_item): void
    {
        $this->uuid_item = $uuid_item instanceof EncargoDiaId
            ? $uuid_item
            : new EncargoDiaId($uuid_item);
    }

    /**
     *
     * @return int|null $iid_enc
     */
    public function getId_enc(): ?int
    {
        return $this->id_enc;
    }

    /**
     *
     * @param int|null $id_enc
     */
    public function setId_enc(?int $id_enc = null): void
    {
        $this->id_enc = $id_enc;
    }

    public function getTstart(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->tstart ?? new NullDateTimeLocal();
    }

    public function setTstart(DateTimeLocal|NullDateTimeLocal|null $tstart): void
    {
        $this->tstart = $tstart;
    }

    public function getTend(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->tend ?? new NullDateTimeLocal();
    }

    public function setTend(DateTimeLocal|NullDateTimeLocal|null $tend): void
    {
        $this->tend = $tend;
    }

    /**
     *
     * @return int|null $iid_nom
     */
    public function getId_nom(): ?int
    {
        return $this->id_nom;
    }

    /**
     *
     * @param int|null $id_nom
     */
    public function setId_nom(?int $id_nom = null): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     *
     * @param string|null $observ
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    /*
     * @deprecated use getStatusVo()
     */
    public function getStatus(): ?int
    {
        return $this->status->value();
    }

    public function getStatusVo(): EncargoDiaStatus
    {
        return $this->status;
    }

    /*
     * @deprecated use setStatusVo()
     */
    public function setStatus(?int $status): void
    {
        $this->status = EncargoDiaStatus::fromNullableInt($status);
    }

    public function setStatusVo(EncargoDiaStatus|int|null $status): void
    {
        $this->status = $status instanceof EncargoDiaStatus
            ? $status
            : EncargoDiaStatus::fromNullableInt($status);
    }

}