<?php

namespace src\procesos\domain\entity;

use core\DatosCampo;
use core\Set;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\shared\domain\traits\Hydratable;


class ProcesoTipo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ProcesoTipoId $id_tipo_proceso;

    private string|null $nom_proceso = null;

    private int|null $sfsv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getProcesoTipoId(): ProcesoTipoId
    {
        return $this->id_tipo_proceso;
    }


    public function setProcesoTipoId(ProcesoTipoId $id_tipo_proceso): void
    {
        $this->id_tipo_proceso = $id_tipo_proceso;
    }

    /**
     * @deprecated use getProcesoTipoId()
     */
    public function getId_tipo_proceso(): int
    {
        return $this->id_tipo_proceso->value();
    }

    /**
     * @deprecated use setProcesoTipoId()
     */
    public function setId_tipo_proceso(int $id_tipo_proceso): void
    {
        $this->id_tipo_proceso = new ProcesoTipoId($id_tipo_proceso);
    }


    public function getNom_proceso(): ?string
    {
        return $this->nom_proceso;
    }


    public function setNom_proceso(?string $nom_proceso = null): void
    {
        $this->nom_proceso = $nom_proceso;
    }


    public function getSfsv(): ?int
    {
        return $this->sfsv;
    }


    public function setSfsv(?int $sfsv = null): void
    {
        $this->sfsv = $sfsv;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo_proceso';
    }

  public function getDatosCampos(): array
    {
        $oActividadFaseSet = new Set();
        $oActividadFaseSet->add($this->getDatosNom_proceso());
        $oActividadFaseSet->add($this->getDatosSfsv());
        return $oActividadFaseSet->getTot();
    }

    private function getDatosNom_proceso(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_proceso');
        $oDatosCampo->setMetodoGet('getNom_proceso');
        $oDatosCampo->setMetodoSet('setNom_proceso');
        $oDatosCampo->setEtiqueta(_("nombre del proceso"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    private function getDatosSfsv(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sfsv');
        $oDatosCampo->setMetodoGet('getSfsv');
        $oDatosCampo->setMetodoSet('setSfsv');
        $oDatosCampo->setEtiqueta(_("sf / sv"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista([1 => _("sv"), 2 => _("sf")]);
        return $oDatosCampo;
    }
}