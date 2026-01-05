<?php

namespace src\actividadcargos\domain\entity;

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\value_objects\ObservacionesCargo;
use src\shared\domain\entity\Entity;
use src\shared\domain\traits\Hydratable;

/**
 * Clase que implementa la entidad d_cargos_activ_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/12/2025
 */
class ActividadCargo extends Entity
{
    use Hydratable;
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;
    /**
     * Id_activ de ActividadCargo
     *
     * @var int
     */
    private int $id_activ;
    /**
     * Id_cargo de ActividadCargo
     *
     * @var int
     */
    private int $id_cargo;
    /**
     * Id_nom de ActividadCargo
     *
     * @var int|null
     */
    private int|null $id_nom = null;
    /**
     * Puede_agd de ActividadCargo
     *
     * @var bool
     */
    private bool $puede_agd;
    /**
     * Observ de ActividadCargo
     *
     * @var string|null
     */
    private string|null $observ = null;
    /**
     * Id_item de ActividadCargo
     *
     * @var int
     */
    private int $id_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/



    protected function getEntityName(): string
    {
        return $this->isSacd() ? 'ActividadCargoSacd' : 'ActividadCargoNoSacd';
    }

    private function isSacd():bool
    {
        $a_id_cargo_sacd = $GLOBALS['container']->get(CargoRepositoryInterface::class)->getArrayIdCargosSacd();
        return in_array($this->id_cargo, $a_id_cargo_sacd);
    }

    public function getId_schema(): int
    {
        return $this->id_schema;
    }

    /**
     *
     * @param int $id_schema
     */
    public function setId_schema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }

    /**
     *
     * @return int $id_activ
     */
    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    /**
     *
     * @param int $id_activ
     */
    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     *
     * @return int $id_cargo
     */
    public function getId_cargo(): int
    {
        return $this->id_cargo;
    }

    /**
     *
     * @param int $id_cargo
     */
    public function setId_cargo(int $id_cargo): void
    {
        $this->id_cargo = $id_cargo;
    }

    /**
     *
     * @return int|null $id_nom
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
     * @return bool $bpuede_agd
     */
    public function isPuede_agd(): bool
    {
        return $this->puede_agd;
    }

    /**
     *
     * @param bool $bpuede_agd
     */
    public function setPuede_agd(bool $bpuede_agd): void
    {
        $this->puede_agd = $bpuede_agd;
    }

    /**
     * @return ObservacionesCargo|null
     */
    public function getObservVo(): ?ObservacionesCargo
    {
        return $this->observ !== null ? new ObservacionesCargo($this->observ) : null;
    }

    /**
     * @param ObservacionesCargo|null $oObservacionesCargo
     */
    public function setObservVo(?ObservacionesCargo $oObservacionesCargo = null): void
    {
        $this->observ = $oObservacionesCargo?->value();
    }

    /**
     *
     * @return string|null $sobserv
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     *
     * @param string|null $sobserv
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->observ = $sobserv;
    }

    /**
     *
     * @return int $id_item
     */
    public function getId_item(): int
    {
        return $this->id_item;
    }

    /**
     *
     * @param int $id_item
     */
    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }
}