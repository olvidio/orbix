<?php

namespace src\actividades\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\value_objects\{RepeticionId, RepeticionText, TemporadaCode, RepeticionTipo};
use src\shared\domain\traits\Hydratable;

/**
 * Clase que implementa la entidad xa_tipo_repeticion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class Repeticion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_repeticion de Repeticion
     *
     * @var RepeticionId
     */
    private RepeticionId $id_repeticion;
    /**
     * Repeticion de Repeticion
     *
     * @var RepeticionText
     */
    private RepeticionText $repeticion;
    /**
     * Temporada de Repeticion
     *
     * @var TemporadaCode
     */
    private TemporadaCode $temporada;
    /**
     * Tipo de Repeticion
     *
     * @var RepeticionTipo|null
     */
    private RepeticionTipo|null $tipo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated usar getId()
     */
    public function getId_repeticion(): int
    {
        return $this->id_repeticion->value();
    }

    /**
     *
     * @param int $id_repeticion
     */
    /**
     * @deprecated usar setId(RepeticionId $id)
     */
    public function setId_repeticion(int $id_repeticion): void
    {
        $this->id_repeticion = new RepeticionId($id_repeticion);
    }

    // Nuevos métodos con Value Objects
    public function getId(): RepeticionId
    {
        return $this->id_repeticion;
    }

    public function setId(RepeticionId $id): void
    {
        $this->id_repeticion = $id;
    }

    /**
     *
     * @return string $repeticion
     */
    /**
     * @deprecated usar getRepeticionVo()
     */
    public function getRepeticion(): string
    {
        return $this->repeticion->value();
    }

    /**
     *
     * @param string $srepeticion
     */
    /**
     * @deprecated usar setRepeticionVo(RepeticionText $repeticion)
     */
    public function setRepeticion(string $repeticion): void
    {
        $this->repeticion = new RepeticionText($repeticion);
    }

    public function getRepeticionVo(): RepeticionText
    {
        return $this->repeticion;
    }

    public function setRepeticionVo(RepeticionText|string|null $texto): void
    {
        $this->repeticion = $texto instanceof RepeticionText
            ? $texto
            : RepeticionText::fromNullableString($texto);
    }

    /**
     *
     * @return string $temporada
     */
    /**
     * @deprecated usar getTemporadaVo()
     */
    public function getTemporada(): string
    {
        return $this->temporada->value();
    }

    /**
     *
     * @param string $stemporada
     */
    /**
     * @deprecated usar setTemporadaVo(TemporadaCode $temporada)
     */
    public function setTemporada(string $temporada): void
    {
        $this->temporada = new TemporadaCode($temporada);
    }

    public function getTemporadaVo(): TemporadaCode
    {
        return $this->temporada;
    }

    public function setTemporadaVo(TemporadaCode|string|null $texto): void
    {
        $this->temporada = $texto instanceof TemporadaCode
            ? $texto
            : TemporadaCode::fromNullableString($texto);
    }

    /**
     *
     * @return int|null $tipo
     */
    /**
     * @deprecated usar getTipoVo()
     */
    public function getTipo(): ?int
    {
        return $this->tipo?->value();
    }

    /**
     *
     * @param int|null $tipo
     */
    /**
     * @deprecated usar setTipoVo(?RepeticionTipo $tipo)
     */
    public function setTipo(?int $tipo = null): void
    {
        $this->tipo = $tipo === null ? null : new RepeticionTipo($tipo);
    }

    public function getTipoVo(): ?RepeticionTipo
    {
        return $this->tipo;
    }

    public function setTipoVo(RepeticionTipo|int|null $tipo): void
    {
        $this->tipo = $tipo instanceof RepeticionTipo
            ? $tipo
            : RepeticionTipo::fromNullable($tipo);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_repeticion';
    }

    public function getDatosCampos(): array
    {
        $oRepeticionSet = new Set();

        $oRepeticionSet->add($this->getDatosRepeticion());
        $oRepeticionSet->add($this->getDatosTemporada());
        $oRepeticionSet->add($this->getDatosTipo());
        return $oRepeticionSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo repeticion de Repeticion
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosRepeticion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('repeticion');
        $oDatosCampo->setMetodoGet('getRepeticion'); // legacy para UI
        $oDatosCampo->setMetodoSet('setRepeticion'); // legacy para UI
        $oDatosCampo->setEtiqueta(_("repetición"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo temporada de Repeticion
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTemporada(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('temporada');
        $oDatosCampo->setMetodoGet('getTemporada'); // legacy para UI
        $oDatosCampo->setMetodoSet('setTemporada'); // legacy para UI
        $oDatosCampo->setEtiqueta(_("temporada"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo tipo de Repeticion
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo');
        $oDatosCampo->setMetodoGet('getTipo'); // legacy para UI
        $oDatosCampo->setMetodoSet('setTipo'); // legacy para UI
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }
}