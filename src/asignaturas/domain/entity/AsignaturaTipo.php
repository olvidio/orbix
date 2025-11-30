<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\value_objects\{AsignaturaTipoId, AsignaturaTipoName, AsignaturaTipoShortName, AsignaturaTipoYear, AsignaturaTipoLatin};

/**
 * Clase que implementa la entidad xa_tipo_asig
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class AsignaturaTipo
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tipo de AsignaturaTipo
     */
    private AsignaturaTipoId $idTipo;
    /**
     * Tipo_asignatura de AsignaturaTipo
     */
    private AsignaturaTipoName $tipoAsignatura;
    /**
     * Tipo_breve de AsignaturaTipo
     */
    private AsignaturaTipoShortName $tipoBreve;
    /**
     * Año de AsignaturaTipo (texto corto)
     */
    private ?AsignaturaTipoYear $year = null;
    /**
     * Tipo_latin de AsignaturaTipo
     */
    private ?AsignaturaTipoLatin $tipoLatin = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return AsignaturaTipo
     */
    public function setAllAttributes(array $aDatos): AsignaturaTipo
    {
        if (array_key_exists('id_tipo', $aDatos)) {
            $this->setIdTipoVo(new AsignaturaTipoId((int)$aDatos['id_tipo']));
        }
        if (array_key_exists('tipo_asignatura', $aDatos)) {
            $this->setTipoAsignaturaVo(AsignaturaTipoName::fromString((string)$aDatos['tipo_asignatura']));
        }
        if (array_key_exists('tipo_breve', $aDatos)) {
            $this->setTipoBreveVo(AsignaturaTipoShortName::fromString((string)$aDatos['tipo_breve']));
        }
        if (array_key_exists('año', $aDatos)) {
            $this->setYearVo(AsignaturaTipoYear::fromNullableString($aDatos['año'] ?? null));
        }
        if (array_key_exists('tipo_latin', $aDatos)) {
            $this->setTipoLatinVo(AsignaturaTipoLatin::fromNullableString($aDatos['tipo_latin'] ?? null));
        }
        return $this;
    }

    // ---------- VO API ----------
    public function getIdTipoVo(): AsignaturaTipoId { return $this->idTipo; }
    public function setIdTipoVo(AsignaturaTipoId $id): void { $this->idTipo = $id; }

    public function getTipoAsignaturaVo(): AsignaturaTipoName { return $this->tipoAsignatura; }
    public function setTipoAsignaturaVo(AsignaturaTipoName $nombre): void { $this->tipoAsignatura = $nombre; }

    public function getTipoBreveVo(): AsignaturaTipoShortName { return $this->tipoBreve; }
    public function setTipoBreveVo(AsignaturaTipoShortName $nombre): void { $this->tipoBreve = $nombre; }

    public function getYearVo(): ?AsignaturaTipoYear { return $this->year; }
    public function setYearVo(?AsignaturaTipoYear $year = null): void { $this->year = $year; }

    public function getTipoLatinVo(): ?AsignaturaTipoLatin { return $this->tipoLatin; }
    public function setTipoLatinVo(?AsignaturaTipoLatin $latin = null): void { $this->tipoLatin = $latin; }

    /**
     *
     * @return int $iid_tipo
     */
    public function getId_tipo(): int
    {
        return $this->idTipo->value();
    }

    /**
     *
     * @param int $iid_tipo
     */
    public function setId_tipo(int $iid_tipo): void
    {
        $this->idTipo = new AsignaturaTipoId($iid_tipo);
    }

    /**
     *
     * @return string $stipo_asignatura
     */
    public function getTipo_asignatura(): string
    {
        return $this->tipoAsignatura->value();
    }

    /**
     *
     * @param string $stipo_asignatura
     */
    public function setTipo_asignatura(string $stipo_asignatura): void
    {
        $this->tipoAsignatura = new AsignaturaTipoName($stipo_asignatura);
    }

    /**
     *
     * @return string $stipo_breve
     */
    public function getTipo_breve(): string
    {
        return $this->tipoBreve->value();
    }

    /**
     *
     * @param string $stipo_breve
     */
    public function setTipo_breve(string $stipo_breve): void
    {
        $this->tipoBreve = new AsignaturaTipoShortName($stipo_breve);
    }

    /**
     *
     * @return string|null $saño
     */
    public function getYear(): ?string
    {
        return $this->year?->value();
    }

    /**
     *
     * @param string|null $year
     */
    public function setYear(?string $year = null): void
    {
        $this->year = AsignaturaTipoYear::fromNullableString($year);
    }

    /**
     *
     * @return string|null $stipo_latin
     */
    public function getTipo_latin(): ?string
    {
        return $this->tipoLatin?->value();
    }

    /**
     *
     * @param string|null $stipo_latin
     */
    public function setTipo_latin(?string $stipo_latin = null): void
    {
        $this->tipoLatin = AsignaturaTipoLatin::fromNullableString($stipo_latin);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo';
    }

    function getDatosCampos()
    {
        $oAsignaturaTipoSet = new Set();

        $oAsignaturaTipoSet->add($this->getDatosTipo_asignatura());
        $oAsignaturaTipoSet->add($this->getDatosTipo_breve());
        $oAsignaturaTipoSet->add($this->getDatosYear());
        $oAsignaturaTipoSet->add($this->getDatosTipo_latin());
        return $oAsignaturaTipoSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stipo_asignatura de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_asignatura()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_asignatura');
        $oDatosCampo->setMetodoGet('getTipo_asignatura');
        $oDatosCampo->setMetodoSet('setTipo_asignatura');
        $oDatosCampo->setEtiqueta(_("tipo de asignatura"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_breve de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_breve()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_breve');
        $oDatosCampo->setMetodoGet('getTipo_breve');
        $oDatosCampo->setMetodoSet('setTipo_breve');
        $oDatosCampo->setEtiqueta(_("tipo breve"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut saño de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosYear()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('año');
        $oDatosCampo->setMetodoGet('getYear');
        $oDatosCampo->setMetodoSet('setYear');
        $oDatosCampo->setEtiqueta(_("año"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_latin de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_latin()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_latin');
        $oDatosCampo->setMetodoGet('getTipo_latin');
        $oDatosCampo->setMetodoSet('setTipo_latin');
        $oDatosCampo->setEtiqueta(_("tipo_latin"));
        return $oDatosCampo;
    }
}