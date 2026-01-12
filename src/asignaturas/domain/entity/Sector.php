<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\value_objects\{DepartamentoId, SectorId, SectorName};
use src\shared\domain\traits\Hydratable;


class Sector
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private SectorId $id_sector;

    private ?DepartamentoId $id_departamento = null;

    private ?SectorName $nombre_sector = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getIdSectorVo(): SectorId
    {
        return $this->id_sector;
    }

    public function setIdSectorVo(SectorId|int $id): void
    {
        $this->id_sector = $id instanceof SectorId
            ? $id
            : SectorId::fromNullableInt($id);
    }


    public function getId_sector(): int
    {
        return $this->id_sector->value();
    }


    public function setId_sector(int $id_sector): void
    {
        $this->id_sector = SectorId::fromNullableInt($id_sector);
    }

    // VO API
    public function getIdDepartamentoVo(): ?DepartamentoId
    {
        return $this->id_departamento;
    }

    public function setIdDepartamentoVo(DepartamentoId|int|null $valor = null): void
    {
        $this->id_departamento = $valor instanceof DepartamentoId
            ? $valor
            : DepartamentoId::fromNullableInt($valor);
    }


    public function getId_departamento(): ?int
    {
        return $this->id_departamento?->value();
    }


    public function setId_departamento(?int $id_departamento = null): void
    {
        $this->id_departamento = $id_departamento !== null ? new DepartamentoId($id_departamento) : null;
    }

    // VO API
    public function getNombreSectorVo(): ?SectorName
    {
        return $this->nombre_sector;
    }

    public function setNombreSectorVo(SectorName|string|null $texto = null): void
    {
        $this->nombre_sector = $texto instanceof SectorName
            ? $texto
            : SectorName::fromNullableString($texto);
    }


    public function getSector(): string
    {
        return $this->nombre_sector->value();
    }


    public function setSector(string $sector): void
    {
        $this->nombre_sector = SectorName::fromString($sector);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_sector';
    }

    public function getDatosCampos(): array
    {
        $oSectorSet = new Set();

        $oSectorSet->add($this->getDatosId_departamento());
        $oSectorSet->add($this->getDatosSector());
        return $oSectorSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo id_departamento de Sector
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_departamento(): DatosCampo
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
     * Recupera las propiedades del atributo sector de Sector
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosSector(): DatosCampo
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