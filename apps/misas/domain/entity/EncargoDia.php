<?php

namespace misas\domain\entity;

use misas\domain\EncargoDiaId;
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

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private EncargoDiaId $uuid_item;
    /**
     * Id_ctr de EncargoDia
     *
     * @var int
     */
    private int $iid_enc;

    private DateTimeLocal|NullDateTimeLocal $tstart;
    private DateTimeLocal|NullDateTimeLocal $tend;
    /**
     * Id_nom de EncargoDia
     *
     * @var int|null
     */
    private int|null $iid_nom = null;
    /**
     * Observ de EncargoDia
     *
     * @var string|null
     */
    private string|null $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoDia
     */
    public function setAllAttributes(array $aDatos): EncargoDia
    {
        if (array_key_exists('uuid_item', $aDatos)) {
            $uuid = new EncargoDiaId($aDatos['uuid_item']);
            $this->setUuid_item($uuid);
        }
        if (array_key_exists('id_enc', $aDatos)) {
            $this->setId_enc($aDatos['id_enc']);
        }
        if (array_key_exists('tstart', $aDatos)) {
            $tstart = $aDatos['tstart']?? new NullDateTimeLocal();
            $this->setTstart($tstart);
        }
        if (array_key_exists('tend', $aDatos)) {
            $tend = $aDatos['tend']?? new NullDateTimeLocal();
            $this->setTend($tend);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        return $this;
    }

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

    public function setTstart(DateTimeLocal|NullDateTimeLocal $tstart): void
    {
        $this->tstart = $tstart;
    }

    public function getTend(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->tend ?? new NullDateTimeLocal();
    }

    public function setTend(DateTimeLocal|NullDateTimeLocal $tend): void
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
}