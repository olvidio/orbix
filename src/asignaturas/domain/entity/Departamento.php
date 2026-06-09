<?php

namespace src\asignaturas\domain\entity;

use src\shared\domain\DatosCampo;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\asignaturas\domain\value_objects\{DepartamentoId, DepartamentoName};
use src\shared\domain\traits\Hydratable;

class Departamento
{
    use Hydratable {
        toArrayForDatabase as private hydratableToArrayForDatabase;
    }

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
        if ($id instanceof DepartamentoId) {
            $this->id_departamento = $id;
            return;
        }
        $vo = DepartamentoId::fromNullableInt($id);
        if ($vo !== null) {
            $this->id_departamento = $vo;
        }
    }


    public function getId_departamento(): int
    {
        return $this->id_departamento->value();
    }


    public function setId_departamento(int $id_departamento): void
    {
        $this->id_departamento = new DepartamentoId($id_departamento);
    }

    // VO API
    public function getNombreDepartamentoVo(): DepartamentoName
    {
        return $this->nombre_departamento;
    }

    public function setNombreDepartamentoVo(DepartamentoName|string $nombre): void
    {
        if ($nombre instanceof DepartamentoName) {
            $this->nombre_departamento = $nombre;
            return;
        }
        $vo = DepartamentoName::fromNullableString($nombre);
        if ($vo !== null) {
            $this->nombre_departamento = $vo;
        }
    }


    public function getDepartamento(): string
    {
        return $this->nombre_departamento->value();
    }


    public function setDepartamento(string $departamento): void
    {
        $this->nombre_departamento = new DepartamentoName($departamento);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_departamento';
    }

    /**
     * @return list<DatosCampo>
     */
    public function getDatosCampos(): array
    {
        $oDepartamentoSet = new Set();

        $oDepartamentoSet->add($this->getDatosDepartamento());
        /** @var list<DatosCampo> $campos */
        $campos = array_values($oDepartamentoSet->getTot());
        return $campos;
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
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    /**
     * La columna en BD es `departamento`; la propiedad VO es `nombre_departamento`.
     */
    /**
     * @param array<string, callable> $converters
     * @return array<string, mixed>
     */
    public function toArrayForDatabase(array $converters = []): array
    {
        $data = $this->hydratableToArrayForDatabase($converters);
        if (array_key_exists('nombre_departamento', $data)) {
            $data['departamento'] = $data['nombre_departamento'];
            unset($data['nombre_departamento']);
        }

        return $data;
    }
}