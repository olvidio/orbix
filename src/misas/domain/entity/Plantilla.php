<?php

namespace src\misas\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;

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
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_ctr;

    private int $tarea;

    private string $dia = '';

    private ?int $semana = null;

    private DateTimeLocal|null $t_start;
    private DateTimeLocal|null $t_end;

    private ?int $id_nom = null;

    private ?string $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $iid_item): void
    {
        $this->id_item = $iid_item;
    }


    public function getId_ctr(): int
    {
        return $this->id_ctr;
    }


    public function setId_ctr(int $iid_ctr): void
    {
        $this->id_ctr = $iid_ctr;
    }


    public function getTarea(): int
    {
        return $this->tarea;
    }


    public function setTarea(int $itarea): void
    {
        $this->tarea = $itarea;
    }


    public function getDia(): string
    {
        return $this->dia;
    }


    public function setDia(string $sdia): void
    {
        $this->dia = $sdia;
    }


    public function getSemana(): ?int
    {
        return $this->semana;
    }


    public function setSemana(?int $isemana = null): void
    {
        $this->semana = $isemana;
    }

    public function getT_start(): DateTimeLocal|null
    {
        return $this->t_start;
    }

    public function setT_start(DateTimeLocal|null $tt_start): void
    {
        $this->t_start = $tt_start;
    }

    public function getT_end(): DateTimeLocal|null
    {
        return $this->t_end;
    }

    public function setT_end(DateTimeLocal|null $tt_end): void
    {
        $this->t_end = $tt_end;
    }


    public function getId_nom(): ?int
    {
        return $this->id_nom;
    }


    public function setId_nom(?int $iid_nom = null): void
    {
        $this->id_nom = $iid_nom;
    }


    public function getObserv(): ?string
    {
        return $this->observ;
    }


    public function setObserv(?string $sobserv = null): void
    {
        $this->observ = $sobserv;
    }
}