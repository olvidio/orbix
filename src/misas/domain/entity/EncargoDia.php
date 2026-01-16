<?php

namespace src\misas\domain\entity;

use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

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
    private int $iid_enc;

    private DateTimeLocal|NullDateTimeLocal|null $tstart;
    private DateTimeLocal|NullDateTimeLocal|null $tend;

    private ?int $iid_nom = null;
    private ?string $sobserv = null;
    private ?EncargoDiaStatus $status = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getUuid_item(): EncargoDiaId
    {
        return $this->uuid_item;
    }

    public function setUuid_item(EncargoDiaId $uuid_item): void
    {
        $this->uuid_item = $uuid_item;
    }

    /**
     *
     * @return int|null $iid_enc
     */
    public function getId_enc(): ?int
    {
        return $this->iid_enc;
    }

    /**
     *
     * @param int|null $iid_enc
     */
    public function setId_enc(?int $iid_enc = null): void
    {
        $this->iid_enc = $iid_enc;
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
        return $this->iid_nom;
    }

    /**
     *
     * @param int|null $iid_nom
     */
    public function setId_nom(?int $iid_nom = null): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
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
    public function setStatus(?int $istatus): void
    {
        $this->status = EncargoDiaStatus::fromNullableInt($istatus);
    }

    public function setStatusVo(EncargoDiaStatus|int|null $status): void
    {
        $this->status = $status instanceof EncargoDiaStatus
            ? $status
            : EncargoDiaStatus::fromNullableInt($status);
    }

}