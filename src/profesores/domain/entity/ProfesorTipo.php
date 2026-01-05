<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\{ProfesorTipoId, ProfesorTipoName};
use src\shared\domain\traits\Hydratable;


class ProfesorTipo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private ProfesorTipoId $id_tipo_profesor;

    private ?ProfesorTipoName $tipo_profesor = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // -------- VO API --------
    public function getIdTipoProfesorVo(): ProfesorTipoId
    {
        return $this->id_tipo_profesor;
    }

    public function setIdTipoProfesorVo(ProfesorTipoId $id): void
    {
        $this->id_tipo_profesor = $id;
    }

    public function getTipoProfesorVo(): ?ProfesorTipoName
    {
        return $this->tipo_profesor;
    }

    public function setTipoProfesorVo(?ProfesorTipoName $nombre = null): void
    {
        $this->tipo_profesor = $nombre;
    }


    public function getId_tipoprofesor(): int
    {
        return $this->id_tipo_profesor->value();
    }


    public function setId_tipoprofesor(int $id_tipo_profesor): void
    {
        $this->id_tipo_profesor = new ProfesorTipoId($id_tipo_profesor);
    }


    public function getTipo_profesor(): ?string
    {
        return $this->tipo_profesor?->value();
    }


    public function setTipo_profesor(?string $tipo_profesor = null): void
    {
        $this->tipo_profesor = ProfesorTipoName::fromNullableString($tipo_profesor);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo_profesor';
    }

  public function getDatosCampos(): array
    {
        $oTipoCentroSet = new Set();

        $oTipoCentroSet->add($this->getDatosId_nom());
        $oTipoCentroSet->add($this->getDatosTipo_profesor());
        return $oTipoCentroSet->getTot();
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

    private function getDatosTipo_profesor(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_profesor');
        $oDatosCampo->setMetodoGet('getTipo_profesor');
        $oDatosCampo->setMetodoSet('setTipo_profesor');
        $oDatosCampo->setEtiqueta(_("tipo de profesor"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }
}