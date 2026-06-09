<?php

namespace src\inventario\domain\entity;

use src\shared\domain\DatosCampo;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\inventario\domain\value_objects\UbiInventarioId;
use src\inventario\domain\value_objects\UbiInventarioIdActiv;
use src\inventario\domain\value_objects\UbiInventarioName;
use src\shared\domain\traits\Hydratable;


class UbiInventario
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private UbiInventarioId $id_ubi;

    private UbiInventarioName $nom_ubi;

    private ?UbiInventarioIdActiv $id_ubi_activ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_ubi(): int
    {
        return $this->id_ubi->value();
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = new UbiInventarioId($id_ubi);
    }


    public function getNom_ubi(): string
    {
        return $this->nom_ubi->value();
    }


    public function setNom_ubi(string $nom_ubi): void
    {
        $this->nom_ubi = new UbiInventarioName($nom_ubi);
    }


    public function getId_ubi_activ(): ?int
    {
        return $this->id_ubi_activ?->value();
    }


    public function setId_ubi_activ(?int $id_ubi_activ = null): void
    {
        $this->id_ubi_activ = UbiInventarioIdActiv::fromNullableInt($id_ubi_activ);
    }

    // Value Object API (duplicada con legacy)
    public function getIdUbiVo(): UbiInventarioId
    {
        return $this->id_ubi;
    }

    public function setIdUbiVo(UbiInventarioId|int $id): void
    {
        $this->id_ubi = $id instanceof UbiInventarioId
            ? $id
            : (UbiInventarioId::fromNullableInt($id) ?? throw new \InvalidArgumentException('id cannot be null'));
    }

    public function getNomUbiVo(): UbiInventarioName
    {
        return $this->nom_ubi;
    }

    public function setNomUbiVo(UbiInventarioName|string $name): void
    {
        $this->nom_ubi = $name instanceof UbiInventarioName
            ? $name
            : (UbiInventarioName::fromNullableString($name) ?? throw new \InvalidArgumentException('name cannot be null'));
    }

    public function getIdUbiActivVo(): ?UbiInventarioIdActiv
    {
        return $this->id_ubi_activ;
    }

    public function setIdUbiActivVo(UbiInventarioIdActiv|int|null $id = null): void
    {
        $this->id_ubi_activ = $id instanceof UbiInventarioIdActiv
            ? $id
            : (UbiInventarioIdActiv::fromNullableInt($id) ?? throw new \InvalidArgumentException('id cannot be null'));
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_ubi';
    }

    /** @return list<DatosCampo> */


    public function getDatosCampos():array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNom_ubi());
                /** @var list<DatosCampo> $campos */
        $campos = array_values($oSet->getTot());
        return $campos;
    }

    private function getDatosNom_ubi(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_ubi');
        $oDatosCampo->setMetodoGet('getNom_ubi');
        $oDatosCampo->setMetodoSet('setNom_ubi');
        $oDatosCampo->setEtiqueta(_("nombre del centro/casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;

    }
}