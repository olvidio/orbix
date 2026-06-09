<?php

namespace src\inventario\domain\entity;

use src\shared\domain\DatosCampo;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\inventario\domain\value_objects\ColeccionAgrupar;
use src\inventario\domain\value_objects\ColeccionId;
use src\inventario\domain\value_objects\ColeccionName;
use src\shared\domain\traits\Hydratable;
use function src\shared\domain\helpers\is_true;

class Coleccion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ColeccionId $id_coleccion;

    private ColeccionName $nom_coleccion;

    private ?bool $agrupar = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_coleccion(): int
    {
        return $this->id_coleccion->value();
    }


    public function setId_coleccion(int $id_coleccion): void
    {
        $this->id_coleccion = new ColeccionId($id_coleccion);
    }

    // Value Object API
    public function getIdColeccionVo(): ?ColeccionId
    {
        return $this->id_coleccion;
    }

    public function setIdColeccionVo(ColeccionId|int|null $id = null): void
    {
        $this->id_coleccion = $id instanceof ColeccionId
            ? $id
            : (ColeccionId::fromNullableInt($id) ?? throw new \InvalidArgumentException('id cannot be null'));
    }


    public function getNom_coleccion(): string
    {
        return $this->nom_coleccion->value();
    }


    public function setNom_coleccion(string $nom_coleccion): void
    {
        $this->nom_coleccion = new ColeccionName($nom_coleccion);
    }

    public function getNomColeccionVo(): ?ColeccionName
    {
        return $this->nom_coleccion;
    }

    public function setNomColeccionVo(ColeccionName|string|null $name = null): void
    {
        $this->nom_coleccion = $name instanceof ColeccionName
            ? $name
            : (ColeccionName::fromNullableString($name) ?? throw new \InvalidArgumentException('name cannot be null'));
    }


    public function isAgrupar(): ?bool
    {
        return $this->agrupar;
    }


    public function setAgrupar(?bool $agrupar = null): void
    {
        $this->agrupar = $agrupar;
    }


    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_coleccion';
    }

    /** @return list<DatosCampo> */


    public function getDatosCampos(): array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNom_coleccion());
        $oSet->add($this->getDatosAgrupar());
                /** @var list<DatosCampo> $campos */
        $campos = array_values($oSet->getTot());
        return $campos;
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