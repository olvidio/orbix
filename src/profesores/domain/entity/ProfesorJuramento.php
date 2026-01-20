<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;

class ProfesorJuramento
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private DateTimeLocal $f_juramento;

    

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(int $valor): void
    {
        $this->id_item = $valor;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $valor): void
    {
        $this->id_nom = $valor;
    }

    public function getF_juramento(): DateTimeLocal
    {
        return $this->f_juramento;
    }

    public function setF_juramento(DateTimeLocal $valor): void
    {
        $this->f_juramento = $valor;
    }

/* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

  public function getDatosCampos(): array
    {
        $oProfesorJuramentoSet = new Set();

        $oProfesorJuramentoSet->add($this->getDatosId_nom());
        $oProfesorJuramentoSet->add($this->getDatosF_juramento());
        return $oProfesorJuramentoSet->getTot();
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

    private function getDatosF_juramento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_juramento');
        $oDatosCampo->setMetodoGet('getF_juramento');
        $oDatosCampo->setMetodoSet('setF_juramento');
        $oDatosCampo->setEtiqueta(_("fecha del juramento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}