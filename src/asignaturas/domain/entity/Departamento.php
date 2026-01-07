<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\value_objects\{DepartamentoId, DepartamentoName};
use src\shared\domain\traits\Hydratable;

class Departamento
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private DepartamentoId $id_departamento;

    private DepartamentoName $nombre_departamento;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getIdDepartamentoVo(): DepartamentoId
    {
        return $this->id_departamento;
    }

    public function setIdDepartamentoVo(DepartamentoId|int $id): void
    {
        $this->id_departamento = $id instanceof DepartamentoId
            ? $id
            : DepartamentoId::fromNullable($id);
    }


    public function getId_departamento(): int
    {
        return $this->id_departamento->value();
    }


    public function setId_departamento(int $id_departamento): void
    {
        $this->id_departamento = DepartamentoId::fromNullable($id_departamento);
    }

    // VO API
    public function getNombreDepartamentoVo(): DepartamentoName
    {
        return $this->nombre_departamento;
    }

    public function setNombreDepartamentoVo(DepartamentoName|string $nombre): void
    {
        $this->nombre_departamento = $nombre instanceof DepartamentoName
            ? $nombre
            : DepartamentoName::fromNullableString($nombre);
    }


    public function getDepartamento(): string
    {
        return $this->nombre_departamento->value() ?? '';
    }


    public function setDepartamento(string $departamento): void
    {
        $this->nombre_departamento = DepartamentoName::fromString($departamento);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_departamento';
    }

    public function getDatosCampos(): array
    {
        $oDepartamentoSet = new Set();

        $oDepartamentoSet->add($this->getDatosDepartamento());
        return $oDepartamentoSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo departamento de Departamento
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosDepartamento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('departamento');
        $oDatosCampo->setMetodoGet('getDepartamento');
        $oDatosCampo->setMetodoSet('setDepartamento');
        $oDatosCampo->setEtiqueta(_("departamento"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }
}