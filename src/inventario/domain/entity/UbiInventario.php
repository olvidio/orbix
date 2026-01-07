<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
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
        $this->id_ubi = UbiInventarioId::fromNullable($id_ubi);
    }


    public function getNom_ubi(): string
    {
        return $this->nom_ubi->value();
    }


    public function setNom_ubi(string $nom_ubi): void
    {
        $this->nom_ubi = UbiInventarioName::fromNullableString($nom_ubi);
    }


    public function getId_ubi_activ(): ?string
    {
        return $this->id_ubi_activ?->value();
    }


    public function setId_ubi_activ(?int $id_ubi_activ = null): void
    {
        $this->id_ubi_activ = UbiInventarioIdActiv::fromNullable($id_ubi_activ);
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
            : UbiInventarioId::fromNullable($id);
    }

    public function getNomUbiVo(): UbiInventarioName
    {
        return $this->nom_ubi;
    }

    public function setNomUbiVo(UbiInventarioName|int $name): void
    {
        $this->nom_ubi = $name instanceof UbiInventarioName
            ? $name
            : UbiInventarioName::fromNullableString($name);
    }

    public function getIdUbiActivVo(): ?UbiInventarioIdActiv
    {
        return $this->id_ubi_activ;
    }

    public function setIdUbiActivVo(UbiInventarioIdActiv|int|null $id = null): void
    {
        $this->id_ubi_activ = $id instanceof UbiInventarioIdActiv
            ? $id
            : UbiInventarioIdActiv::fromNullable($id);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key():string
    {
        return 'id_ubi';
    }

    public function getDatosCampos():array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNom_ubi());
        return $oSet->getTot();
    }

    private function getDatosNom_ubi(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_ubi');
        $oDatosCampo->setMetodoGet('getNom_ubi');
        $oDatosCampo->setMetodoSet('setNom_ubi');
        $oDatosCampo->setEtiqueta(_("nombre del centro/casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;

    }
}