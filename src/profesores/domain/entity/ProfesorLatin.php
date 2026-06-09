<?php

namespace src\profesores\domain\entity;

use src\shared\domain\DatosCampo;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\domain\traits\Hydratable;
use function src\shared\domain\helpers\is_true;

class ProfesorLatin
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_nom;

    private bool $latin;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function isLatin(): bool
    {
        return $this->latin;
    }


    public function setLatin(bool $latin): void
    {
        $this->latin = $latin;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_nom';
    }

  /** @return list<DatosCampo> */


  public function getDatosCampos(): array
    {
        $oProfesorLatinSet = new Set();

        $oProfesorLatinSet->add($this->getDatosId_nom());
        $oProfesorLatinSet->add($this->getDatosLatin());
        /** @var list<DatosCampo> $campos */
        $campos = array_values($oProfesorLatinSet->getTot());
        return $campos;
    }

    private function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        $oDatosCampo->setTipo('hidden');

        return $oDatosCampo;
    }

    private function getDatosLatin(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('latin');
        $oDatosCampo->setMetodoGet('isLatin');
        $oDatosCampo->setMetodoSet('setLatin');
        $oDatosCampo->setEtiqueta(_("latín"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}