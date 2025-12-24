<?php

namespace src\actividadcargos\domain\entity;

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\domain\traits\EmitsDomainEvents;
use function core\is_true;

/**
 * Clase que implementa la entidad d_cargos_activ_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/12/2025
 */
class ActividadCargo
{

    use EmitsDomainEvents;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $iid_schema;
    /**
     * Id_activ de ActividadCargo
     *
     * @var int
     */
    private int $iid_activ;
    /**
     * Id_cargo de ActividadCargo
     *
     * @var int
     */
    private int $iid_cargo;
    /**
     * Id_nom de ActividadCargo
     *
     * @var int|null
     */
    private int|null $iid_nom = null;
    /**
     * Puede_agd de ActividadCargo
     *
     * @var bool
     */
    private bool $bpuede_agd;
    /**
     * Observ de ActividadCargo
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * Id_item de ActividadCargo
     *
     * @var int
     */
    private int $iid_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ActividadCargo
     */
    public function setAllAttributes(array $aDatos): ActividadCargo
    {
        if (array_key_exists('id_schema', $aDatos)) {
            $this->setId_schema($aDatos['id_schema']);
        }
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('id_cargo', $aDatos)) {
            $this->setId_cargo($aDatos['id_cargo']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('puede_agd', $aDatos)) {
            $this->setPuede_agd(is_true($aDatos['puede_agd']));
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        return $this;
    }


    private function isSacd() {
        $a_id_cargo_sacd = $GLOBALS['container']->get(CargoRepositoryInterface::class)->getArrayIdCargosSacd();
        return in_array($this->iid_cargo, $a_id_cargo_sacd);
    }
    /**
     * Marca esta entidad como nueva (INSERT) y emite el evento correspondiente
     *
     * @param array $datosActuales Datos actuales de la entidad (opcional para contexto)
     * @return void
     */
    public function marcarComoNueva(array $datosActuales = []): void
    {
        $datosNuevos = $this->toArray();

        $EntidadName = $this->isSacd() ? 'ActividadCargoSacd' : 'ActividadCargoNoSacd';

        $this->recordEvent(new EntidadModificada(
            objeto: $EntidadName,
            tipoCambio: 'INSERT',
            idActiv: $this->iid_activ,
            datosNuevos: $datosNuevos,
            datosActuales: $datosActuales
        ));
    }

    /**
     * Marca esta entidad como modificada (UPDATE) y emite el evento correspondiente
     *
     * @param array $datosActuales Datos anteriores antes de la modificación
     * @return void
     */
    public function marcarComoModificada(array $datosActuales): void
    {
        $datosNuevos = $this->toArray();

        $EntidadName = $this->isSacd() ? 'ActividadCargoSacd' : 'ActividadCargoNoSacd';

        $this->recordEvent(new EntidadModificada(
            objeto: $EntidadName,
            tipoCambio: 'UPDATE',
            idActiv: $this->iid_activ,
            datosNuevos: $datosNuevos,
            datosActuales: $datosActuales
        ));
    }

    /**
     * Marca esta entidad como eliminada (DELETE) y emite el evento correspondiente
     *
     * @param array $datosActuales Datos actuales antes de eliminar
     * @return void
     */
    public function marcarComoEliminada(array $datosActuales): void
    {
        $EntidadName = $this->isSacd() ? 'ActividadCargoSacd' : 'ActividadCargoNoSacd';

        $this->recordEvent(new EntidadModificada(
            objeto: $EntidadName,
            tipoCambio: 'DELETE',
            idActiv: $this->iid_activ,
            datosNuevos: [],
            datosActuales: $datosActuales
        ));
    }

    /**
     * Convierte la entidad a un array asociativo
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_activ' => $this->iid_activ,
            'id_cargo' => $this->iid_cargo,
            'id_nom' => $this->iid_nom,
            'puede_agd' => $this->bpuede_agd,
            'observ' => $this->sobserv,
        ];
    }

    public function getId_schema(): int
    {
        return $this->iid_schema;
    }

    /**
     *
     * @param int $iid_schema
     */
    public function setId_schema(int $iid_schema): void
    {
        $this->iid_schema = $iid_schema;
    }

    /**
     *
     * @return int $iid_activ
     */
    public function getId_activ(): int
    {
        return $this->iid_activ;
    }

    /**
     *
     * @param int $iid_activ
     */
    public function setId_activ(int $iid_activ): void
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     *
     * @return int $iid_cargo
     */
    public function getId_cargo(): int
    {
        return $this->iid_cargo;
    }

    /**
     *
     * @param int $iid_cargo
     */
    public function setId_cargo(int $iid_cargo): void
    {
        $this->iid_cargo = $iid_cargo;
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
     * @return bool $bpuede_agd
     */
    public function isPuede_agd(): bool
    {
        return $this->bpuede_agd;
    }

    /**
     *
     * @param bool $bpuede_agd
     */
    public function setPuede_agd(bool $bpuede_agd): void
    {
        $this->bpuede_agd = $bpuede_agd;
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
}