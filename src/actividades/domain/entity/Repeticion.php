<?php

namespace src\actividades\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\value_objects\{RepeticionId, RepeticionText, TemporadaCode, RepeticionTipo};

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

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_repeticion de Repeticion
     *
     * @var RepeticionId
     */
    private RepeticionId $iid_repeticion;
    /**
     * Repeticion de Repeticion
     *
     * @var RepeticionText
     */
    private RepeticionText $srepeticion;
    /**
     * Temporada de Repeticion
     *
     * @var TemporadaCode
     */
    private TemporadaCode $stemporada;
    /**
     * Tipo de Repeticion
     *
     * @var RepeticionTipo|null
     */
    private RepeticionTipo|null $itipo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Repeticion
     */
    public function setAllAttributes(array $aDatos): Repeticion
    {
        if (array_key_exists('id_repeticion', $aDatos)) {
            $val = $aDatos['id_repeticion'];
            if ($val instanceof RepeticionId) {
                $this->setId($val);
            } else {
                $this->setId_repeticion((int)$val);
            }
        }
        if (array_key_exists('repeticion', $aDatos)) {
            $val = $aDatos['repeticion'];
            if ($val instanceof RepeticionText) {
                $this->setRepeticionVO($val);
            } else {
                $this->setRepeticion((string)$val);
            }
        }
        if (array_key_exists('temporada', $aDatos)) {
            $val = $aDatos['temporada'];
            if ($val instanceof TemporadaCode) {
                $this->setTemporadaVO($val);
            } else {
                $this->setTemporada((string)$val);
            }
        }
        if (array_key_exists('tipo', $aDatos)) {
            $val = $aDatos['tipo'];
            if ($val instanceof RepeticionTipo) {
                $this->setTipoVO($val);
            } else {
                $this->setTipo($val === null ? null : (int)$val);
            }
        }
        return $this;
    }

    /**
     *
     * @return int $iid_repeticion
     */
    /**
     * @deprecated usar getId()
     */
    public function getId_repeticion(): int
    {
        return $this->iid_repeticion->value();
    }

    /**
     *
     * @param int $iid_repeticion
     */
    /**
     * @deprecated usar setId(RepeticionId $id)
     */
    public function setId_repeticion(int $iid_repeticion): void
    {
        $this->iid_repeticion = new RepeticionId($iid_repeticion);
    }

    // Nuevos métodos con Value Objects
    public function getId(): RepeticionId
    {
        return $this->iid_repeticion;
    }

    public function setId(RepeticionId $id): void
    {
        $this->iid_repeticion = $id;
    }

    /**
     *
     * @return string $srepeticion
     */
    /**
     * @deprecated usar getRepeticionVO()
     */
    public function getRepeticion(): string
    {
        return $this->srepeticion->value();
    }

    /**
     *
     * @param string $srepeticion
     */
    /**
     * @deprecated usar setRepeticionVO(RepeticionText $repeticion)
     */
    public function setRepeticion(string $srepeticion): void
    {
        $this->srepeticion = new RepeticionText($srepeticion);
    }

    public function getRepeticionVO(): RepeticionText
    {
        return $this->srepeticion;
    }

    public function setRepeticionVO(RepeticionText $repeticion): void
    {
        $this->srepeticion = $repeticion;
    }

    /**
     *
     * @return string $stemporada
     */
    /**
     * @deprecated usar getTemporadaVO()
     */
    public function getTemporada(): string
    {
        return $this->stemporada->value();
    }

    /**
     *
     * @param string $stemporada
     */
    /**
     * @deprecated usar setTemporadaVO(TemporadaCode $temporada)
     */
    public function setTemporada(string $stemporada): void
    {
        $this->stemporada = new TemporadaCode($stemporada);
    }

    public function getTemporadaVO(): TemporadaCode
    {
        return $this->stemporada;
    }

    public function setTemporadaVO(TemporadaCode $temporada): void
    {
        $this->stemporada = $temporada;
    }

    /**
     *
     * @return int|null $itipo
     */
    /**
     * @deprecated usar getTipoVO()
     */
    public function getTipo(): ?int
    {
        return $this->itipo?->value();
    }

    /**
     *
     * @param int|null $itipo
     */
    /**
     * @deprecated usar setTipoVO(?RepeticionTipo $tipo)
     */
    public function setTipo(?int $itipo = null): void
    {
        $this->itipo = $itipo === null ? null : new RepeticionTipo($itipo);
    }

    public function getTipoVO(): ?RepeticionTipo
    {
        return $this->itipo;
    }

    public function setTipoVO(?RepeticionTipo $tipo): void
    {
        $this->itipo = $tipo;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_repeticion';
    }

    function getDatosCampos()
    {
        $oRepeticionSet = new Set();

        $oRepeticionSet->add($this->getDatosRepeticion());
        $oRepeticionSet->add($this->getDatosTemporada());
        $oRepeticionSet->add($this->getDatosTipo());
        return $oRepeticionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut srepeticion de Repeticion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosRepeticion()
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
     * Recupera les propietats de l'atribut stemporada de Repeticion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTemporada()
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
     * Recupera les propietats de l'atribut stipo de Repeticion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo()
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