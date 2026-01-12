<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\CentroDntName;
use src\profesores\domain\value_objects\PublicacionTitulo;
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

    private PublicacionTitulo $titulo;

    private ?CentroDntName $centro_dnt = null;

    private ?bool $eclesiastico = null;

    private ?YearNumber $year = null;

    

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getTituloVo(): PublicacionTitulo
    {
        return $this->titulo;
    }

    public function setTituloVo(PublicacionTitulo|string $valor = null): void
    {
        $this->titulo = $valor instanceof PublicacionTitulo
            ? $valor
            : PublicacionTitulo::fromNullableString($valor);
    }

    /**
     * @deprecated use getTituloVo()
     */
    public function getTitulo(): string
    {
        return $this->titulo->value();
    }

    /**
     * @deprecated use setTituloVo()
     */
    public function setTitulo(string $valor = null): void
    {
        $this->titulo = PublicacionTitulo::fromNullableString($valor);
    }

    public function getCentroDntVo(): ?CentroDntName
    {
        return $this->centro_dnt;
    }

    public function setCentroDntVo(CentroDntName|string|null $valor = null): void
    {
        $this->centro_dnt = $valor instanceof CentroDntName
            ? $valor
            : CentroDntName::fromNullableString($valor);
    }

    /**
     * @deprecated use getCentroDntVo()
     */
    public function getCentro_dnt(): ?string
    {
        return $this->centro_dnt?->value();
    }

    /**
     * @deprecated use setCentroDntVo()
     */
    public function setCentro_dnt(?string $valor = null): void
    {
        $this->centro_dnt = CentroDntName::fromNullableString($valor);
    }

    public function getYearVo(): ?YearNumber
    {
        return $this->year;
    }

    public function setYearVo(YearNumber|int|null $valor = null): void
    {
        $this->year = $valor instanceof YearNumber
            ? $valor
            : YearNumber::fromNullableInt($valor);
    }

    /**
     * @deprecated use getYearVo()
     */
    public function getYear(): ?int
    {
        return $this->year?->value();
    }

    /**
     * @deprecated use setYearVo()
     */
    public function setYear(?int $valor = null): void
    {
        $this->year = YearNumber::fromNullableInt($valor);
    }

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

    public function isEclesiastico(): ?bool
    {
        return $this->eclesiastico;
    }

    public function setEclesiastico(?bool $valor): void
    {
        $this->eclesiastico = $valor;
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