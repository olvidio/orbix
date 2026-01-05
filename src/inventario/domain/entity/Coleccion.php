<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use src\inventario\domain\value_objects\ColeccionAgrupar;
use src\inventario\domain\value_objects\ColeccionId;
use src\inventario\domain\value_objects\ColeccionName;
use src\shared\domain\traits\Hydratable;
use function core\is_true;

class Coleccion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_coleccion;

    private string $nom_coleccion;

    private bool|null $agrupar = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_coleccion(): int
    {
        return $this->id_coleccion;
    }


    public function setId_coleccion(int $id_coleccion): void
    {
        $this->id_coleccion = $id_coleccion;
    }


    public function getNom_coleccion(): string
    {
        return $this->nom_coleccion;
    }


    public function setNom_coleccion(string $nom_coleccion): void
    {
        $this->nom_coleccion = $nom_coleccion;
    }


    public function isAgrupar(): ?bool
    {
        return $this->agrupar;
    }


    public function setAgrupar(?bool $agrupar = null): void
    {
        $this->agrupar = $agrupar;
    }

    // Value Object API (duplicada con legacy)
    public function getIdColeccionVo(): ?ColeccionId
    {
        return isset($this->id_coleccion) ? new ColeccionId($this->id_coleccion) : null;
    }

    public function setIdColeccionVo(?ColeccionId $id = null): void
    {
        if ($id === null) {
            // dejar como está; id puede ser seteado por repos
            return;
        }
        $this->id_coleccion = $id->value();
    }

    public function getNomColeccionVo(): ?ColeccionName
    {
        return isset($this->nom_coleccion) && $this->nom_coleccion !== '' ? new ColeccionName($this->nom_coleccion) : null;
    }

    public function setNomColeccionVo(?ColeccionName $name = null): void
    {
        $this->nom_coleccion = $name?->value() ?? '';
    }

    public function getAgruparVo(): ?ColeccionAgrupar
    {
        return isset($this->agrupar) ? new ColeccionAgrupar((bool)$this->agrupar) : null;
    }

    public function setAgruparVo(?ColeccionAgrupar $agrupar = null): void
    {
        $this->agrupar = $agrupar?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_coleccion';
    }

    public function getDatosCampos(): array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNom_coleccion());
        $oSet->add($this->getDatosAgrupar());
        return $oSet->getTot();
    }

    private function getDatosNom_coleccion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_coleccion');
        $oDatosCampo->setMetodoGet('getNom_coleccion');
        $oDatosCampo->setMetodoSet('setNom_coleccion');
        $oDatosCampo->setEtiqueta(_("nombre colección"));
        $oDatosCampo->setTipo('texto');
        return $oDatosCampo;
    }

    private function getDatosAgrupar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('agrupar');
        $oDatosCampo->setMetodoGet('isAgrupar');
        $oDatosCampo->setMetodoSet('setAgrupar');
        $oDatosCampo->setEtiqueta(_("agrupar"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}