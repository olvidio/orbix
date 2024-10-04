<?php

namespace misas\domain\entity;

use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad misa_plantillas_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
class Plantilla
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de Plantilla
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_ctr de Plantilla
     *
     * @var int
     */
    private int $iid_ctr;
    /**
     * Tarea de Plantilla
     *
     * @var int
     */
    private int $itarea;
    /**
     * Dia de Plantilla
     *
     * @var string
     */
    private string $sdia = '';
    /**
     * Semana de Plantilla
     *
     * @var int|null
     */
    private int|null $isemana = null;

    private DateTimeLocal|NullDateTimeLocal $tt_start;
    private DateTimeLocal|NullDateTimeLocal $tt_end;
    /**
     * Id_nom de Plantilla
     *
     * @var int|null
     */
    private int|null $iid_nom = null;
    /**
     * Observ de Plantilla
     *
     * @var string|null
     */
    private string|null $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Plantilla
     */
    public function setAllAttributes(array $aDatos): Plantilla
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_ctr', $aDatos)) {
            $this->setId_ctr($aDatos['id_ctr']);
        }
        if (array_key_exists('tarea', $aDatos)) {
            $this->setTarea($aDatos['tarea']);
        }
        if (array_key_exists('dia', $aDatos)) {
            $this->setDia($aDatos['dia']);
        }
        if (array_key_exists('semana', $aDatos)) {
            $this->setSemana($aDatos['semana']);
        }
        if (array_key_exists('t_start', $aDatos)) {
            $t_start = $aDatos['t_start']?? new NullDateTimeLocal();
            $this->setT_start($t_start);
        }
        if (array_key_exists('t_end', $aDatos)) {
            $t_end = $aDatos['t_end']?? new NullDateTimeLocal();
            $this->setT_end($t_end);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_item
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
    }

    /**
     *
     * @return int $iid_ctr
     */
    public function getId_ctr(): int
    {
        return $this->iid_ctr;
    }

    /**
     *
     * @param int $iid_ctr
     */
    public function setId_ctr(int $iid_ctr): void
    {
        $this->iid_ctr = $iid_ctr;
    }

    /**
     *
     * @return int $itarea
     */
    public function getTarea(): int
    {
        return $this->itarea;
    }

    /**
     *
     * @param int $itarea
     */
    public function setTarea(int $itarea): void
    {
        $this->itarea = $itarea;
    }

    /**
     *
     * @return string $sdia
     */
    public function getDia(): string
    {
        return $this->sdia;
    }

    /**
     *
     * @param string $sdia
     */
    public function setDia(string $sdia): void
    {
        $this->sdia = $sdia;
    }

    /**
     *
     * @return int|null $isemana
     */
    public function getSemana(): ?int
    {
        return $this->isemana;
    }

    /**
     *
     * @param int|null $isemana
     */
    public function setSemana(?int $isemana = null): void
    {
        $this->isemana = $isemana;
    }

    public function getT_start(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->tt_start ?? new NullDateTimeLocal();
    }

    public function setT_start(DateTimeLocal|NullDateTimeLocal $tt_start): void
    {
        $this->tt_start = $tt_start;
    }

    public function getT_end(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->tt_end ?? new NullDateTimeLocal();
    }

    public function setT_end(DateTimeLocal|NullDateTimeLocal $tt_end): void
    {
        $this->tt_end = $tt_end;
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