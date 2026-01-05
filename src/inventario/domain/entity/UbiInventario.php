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

    private int $id_ubi;

    private string $nom_ubi;

    private int|null $id_ubi_activ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getNom_ubi(): string
    {
        return $this->nom_ubi;
    }


    public function setNom_ubi(string $nom_ubi): void
    {
        $this->nom_ubi = $nom_ubi;
    }


    public function getId_ubi_activ(): ?int
    {
        return $this->id_ubi_activ;
    }


    public function setId_ubi_activ(?int $id_ubi_activ = null): void
    {
        $this->id_ubi_activ = $id_ubi_activ;
    }

    // Value Object API (duplicada con legacy)
    public function getIdUbiVo(): UbiInventarioId
    {
        return new UbiInventarioId($this->id_ubi);
    }

    public function setIdUbiVo(UbiInventarioId $id): void
    {
        $this->id_ubi = $id->value();
    }

    public function getNomUbiVo(): UbiInventarioName
    {
        return new UbiInventarioName($this->nom_ubi);
    }

    public function setNomUbiVo(UbiInventarioName $name): void
    {
        $this->nom_ubi = $name->value();
    }

    public function getIdUbiActivVo(): ?UbiInventarioIdActiv
    {
        return new UbiInventarioIdActiv($this->id_ubi_activ);
    }

    public function setIdUbiActivVo(?UbiInventarioIdActiv $id = null): void
    {
        $this->id_ubi_activ = $id?->value();
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