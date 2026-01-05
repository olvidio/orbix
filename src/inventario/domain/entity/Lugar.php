<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\value_objects\LugarName;
use src\shared\domain\traits\Hydratable;


class Lugar
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_lugar;

    private int $id_ubi;

    private string $nom_lugar;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_lugar(): int
    {
        return $this->id_lugar;
    }


    public function setId_lugar(int $id_lugar): void
    {
        $this->id_lugar = $id_lugar;
    }


    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getNom_lugar(): string
    {
        return $this->nom_lugar;
    }


    public function setNom_lugar(string $nom_lugar): void
    {
        $this->nom_lugar = $nom_lugar;
    }

    // Value Object API (duplicada con legacy)
    public function getNomLugarVo(): LugarName
    {
        return new LugarName($this->nom_lugar);
    }

    public function setNomLugarVo(LugarName $name): void
    {
        $this->nom_lugar = $name->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_lugar';
    }

    public function getDatosCampos():array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosId_ubi());
        $oSet->add($this->getDatosNom_lugar());
        return $oSet->getTot();
    }

    private function getDatosId_ubi(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ubi');
        $oDatosCampo->setMetodoGet('getId_ubi');
        $oDatosCampo->setMetodoSet('setId_ubi');
        $oDatosCampo->setEtiqueta(_("centro/casa"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(UbiInventarioRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNom_ubi');
        $oDatosCampo->setArgument3('getArrayUbisInventario');
        return $oDatosCampo;
    }

    private function getDatosNom_lugar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_lugar');
        $oDatosCampo->setMetodoGet('getNom_lugar');
        $oDatosCampo->setMetodoSet('setNom_lugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

}