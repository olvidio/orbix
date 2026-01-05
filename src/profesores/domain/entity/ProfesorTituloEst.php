<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\CentroDntName;
use src\profesores\domain\value_objects\TituloName;
use src\profesores\domain\value_objects\YearNumber;
use src\shared\domain\traits\Hydratable;
use function core\is_true;


class ProfesorTituloEst
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private string $titulo;

    private string|null $centro_dnt = null;

    private bool|null $eclesiastico = null;

    private int|null $year = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated Usar getTituloVo()->value()
     */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * @deprecated Usar setTituloVo(TituloName $vo)
     */
    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getTituloVo(): TituloName
    {
        return new TituloName($this->titulo);
    }

    public function setTituloVo(?TituloName $titulo): void
    {
        if ($titulo !== null) {
            $this->titulo = $titulo->value();
        }
    }

    /**
     * @deprecated Usar getCentroDntVo()->value()
     */
    public function getCentro_dnt(): ?string
    {
        return $this->centro_dnt;
    }

    /**
     * @deprecated Usar setCentroDntVo(CentroDntName $vo)
     */
    public function setCentro_dnt(?string $centro_dnt = null): void
    {
        $this->centro_dnt = $centro_dnt;
    }

    public function getCentroDntVo(): ?CentroDntName
    {
        return CentroDntName::fromNullable($this->centro_dnt);
    }

    public function setCentroDntVo(?CentroDntName $centro): void
    {
        $this->centro_dnt = $centro?->value();
    }


    public function isEclesiastico(): ?bool
    {
        return $this->eclesiastico;
    }


    public function setEclesiastico(?bool $eclesiastico = null): void
    {
        $this->eclesiastico = $eclesiastico;
    }

    /**
     * @deprecated Usar getYearVo()->value()
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @deprecated Usar setYearVo(YearNumber $vo)
     */
    public function setYear(?int $year = null): void
    {
        $this->year = $year;
    }

    public function getYearVo(): ?YearNumber
    {
        return YearNumber::fromNullable($this->year);
    }

    public function setYearVo(?YearNumber $year): void
    {
        $this->year = $year?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

  public function getDatosCampos(): array
    {
        $oProfesorTituloEstSet = new Set();

        $oProfesorTituloEstSet->add($this->getDatosId_nom());
        $oProfesorTituloEstSet->add($this->getDatosTitulo());
        $oProfesorTituloEstSet->add($this->getDatosCentro_dnt());
        $oProfesorTituloEstSet->add($this->getDatosEclesiastico());
        $oProfesorTituloEstSet->add($this->getDatosYear());
        return $oProfesorTituloEstSet->getTot();
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

    private function getDatosTitulo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('titulo');
        $oDatosCampo->setMetodoGet('getTitulo');
        $oDatosCampo->setMetodoSet('setTitulo');
        $oDatosCampo->setEtiqueta(_("título"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    private function getDatosCentro_dnt(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('centro_dnt');
        $oDatosCampo->setMetodoGet('getCentro_dnt');
        $oDatosCampo->setMetodoSet('setCentro_dnt');
        $oDatosCampo->setEtiqueta(_("centro docente"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    private function getDatosEclesiastico(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('eclesiastico');
        $oDatosCampo->setMetodoGet('isEclesiastico');
        $oDatosCampo->setMetodoSet('setEclesiastico');
        $oDatosCampo->setEtiqueta(_("eclesiástico"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosYear(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('year');
        $oDatosCampo->setMetodoGet('getYear');
        $oDatosCampo->setMetodoSet('setYear');
        $oDatosCampo->setEtiqueta(_("año"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }
}