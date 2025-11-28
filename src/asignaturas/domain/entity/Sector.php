<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\value_objects\{SectorId, SectorName, DepartamentoId};
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;

/**
 * Clase que implementa la entidad xe_sectores
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class Sector
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id del Sector
     */
    private SectorId $idSector;
    /**
     * Id del Departamento (FK)
     */
    private ?DepartamentoId $idDepartamento = null;
    /**
     * Nombre del Sector
     */
    private ?SectorName $nombreSector = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Sector
     */
    public function setAllAttributes(array $aDatos): Sector
    {
        if (array_key_exists('id_sector', $aDatos)) {
            $this->setIdSectorVo(new SectorId((int)$aDatos['id_sector']));
        }
        if (array_key_exists('id_departamento', $aDatos)) {
            $valor = $aDatos['id_departamento'] ?? null;
            $this->setIdDepartamentoVo(isset($valor) && $valor !== '' ? new DepartamentoId((int)$valor) : null);
        }
        if (array_key_exists('sector', $aDatos)) {
            $this->setNombreSectorVo(SectorName::fromNullableString($aDatos['sector'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getIdSectorVo(): SectorId
    {
        return $this->idSector;
    }

    public function setIdSectorVo(SectorId $id): void
    {
        $this->idSector = $id;
    }

    /**
     *
     * @return int $iid_sector
     */
    public function getId_sector(): int
    {
        return $this->idSector->value();
    }

    /**
     *
     * @param int $iid_sector
     */
    public function setId_sector(int $iid_sector): void
    {
        $this->idSector = new SectorId($iid_sector);
    }

    // VO API
    public function getIdDepartamentoVo(): ?DepartamentoId
    {
        return $this->idDepartamento;
    }

    public function setIdDepartamentoVo(?DepartamentoId $id = null): void
    {
        $this->idDepartamento = $id;
    }

    /**
     *
     * @return int|null $iid_departamento
     */
    public function getId_departamento(): ?int
    {
        return $this->idDepartamento?->value();
    }

    /**
     *
     * @param int|null $iid_departamento
     */
    public function setId_departamento(?int $iid_departamento = null): void
    {
        $this->idDepartamento = $iid_departamento !== null ? new DepartamentoId($iid_departamento) : null;
    }

    // VO API
    public function getNombreSectorVo(): ?SectorName
    {
        return $this->nombreSector;
    }

    public function setNombreSectorVo(?SectorName $nombre = null): void
    {
        $this->nombreSector = $nombre;
    }

    /**
     *
     * @return string $ssector
     */
    public function getSector(): string
    {
        return $this->nombreSector?->value() ?? '';
    }

    /**
     *
     * @param string $ssector
     */
    public function setSector(string $ssector): void
    {
        $this->nombreSector = SectorName::fromString($ssector);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_sector';
    }

    function getDatosCampos()
    {
        $oSectorSet = new Set();

        $oSectorSet->add($this->getDatosId_departamento());
        $oSectorSet->add($this->getDatosSector());
        return $oSectorSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut iid_departamento de Sector
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_departamento()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_departamento');
        $oDatosCampo->setMetodoGet('getId_departamento');
        $oDatosCampo->setMetodoSet('setId_departamento');
        $oDatosCampo->setEtiqueta(_("departamento"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(DepartamentoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getDepartamento'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayDepartamentos');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ssector de Sector
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSector()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sector');
        $oDatosCampo->setMetodoGet('getSector');
        $oDatosCampo->setMetodoSet('setSector');
        $oDatosCampo->setEtiqueta(_("sector"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }
}