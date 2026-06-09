<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\DatosCampo;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\domain\traits\Hydratable;
use src\usuarios\domain\value_objects\Idioma;
use src\usuarios\domain\value_objects\IdLocale;
use src\usuarios\domain\value_objects\NombreIdioma;
use src\usuarios\domain\value_objects\NombreLocale;

class Local
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private IdLocale $id_locale;

    private ?NombreLocale $nom_locale = null;

    private ?Idioma $idioma = null;

    private ?NombreIdioma $nom_idioma = null;

    private bool $active;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getIdLocaleVo(): IdLocale
    {
        return $this->id_locale;
    }


    public function setIdLocaleVo(IdLocale|string $id_locale): void
    {
        $this->id_locale = $id_locale instanceof IdLocale
            ? $id_locale
            : new IdLocale($id_locale);
    }


    public function getIdLocaleAsString(): string
    {
        return $this->id_locale->value();
    }


    public function getNomLocaleVo(): ?NombreLocale
    {
        return $this->nom_locale;
    }


    public function setNomLocaleVo(NombreLocale|string|null $nom_locale = null): void
    {
        if ($nom_locale === null) {
            $this->nom_locale = null;
            return;
        }

        $this->nom_locale = $nom_locale instanceof NombreLocale
            ? $nom_locale
            : new NombreLocale($nom_locale);
    }


    public function getNomLocaleAsString(): ?string
    {
        return $this->nom_locale?->value();
    }


    public function getIdiomaVo(): ?Idioma
    {
        return $this->idioma;
    }


    public function setIdiomaVo(Idioma|string|null $idioma = null): void
    {
        if ($idioma === null) {
            $this->idioma = null;
            return;
        }
        $this->idioma = $idioma instanceof Idioma
            ? $idioma
            : new Idioma($idioma);
    }


    public function getIdiomaAsString(): ?string
    {
        return $this->idioma?->value();
    }


    public function getNomIdiomaVo(): ?NombreIdioma
    {
        return $this->nom_idioma;
    }


    public function setNomIdiomaVo(NombreIdioma|string|null $nom_idioma = null): void
    {
        if ($nom_idioma === null) {
            $this->nom_idioma = null;
            return;
        }
        $this->nom_idioma = $nom_idioma instanceof NombreIdioma
            ? $nom_idioma
            : new NombreIdioma($nom_idioma);
    }


    public function getNomIdiomaAsString(): ?string
    {
        return $this->nom_idioma?->value();
    }


    public function isActive(): bool
    {
        return $this->active;
    }


    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'idLocaleVo';
    }

    /**
     * @return list<mixed>
     */
    public function getDatosCampos(): array
    {
        $oLocalSet = new Set();

        $oLocalSet->add($this->getDatosIdLocale());
        $oLocalSet->add($this->getDatosNom_locale());
        $oLocalSet->add($this->getDatosIdioma());
        $oLocalSet->add($this->getDatosNom_idioma());
        $oLocalSet->add($this->getDatosActive());
        return array_values($oLocalSet->getTot());
    }

    private function getDatosIdLocale(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_locale');
        $oDatosCampo->setMetodoGet('getIdLocaleVo');
        $oDatosCampo->setMetodoSet('setIdLocaleVo');
        $oDatosCampo->setEtiqueta(_("sigla de locale"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('12');
        return $oDatosCampo;
    }
    private function getDatosNom_locale(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_locale');
        $oDatosCampo->setMetodoGet('getNomLocaleVo');
        $oDatosCampo->setMetodoSet('setNomLocaleVo');
        $oDatosCampo->setEtiqueta(_("nombre de locale"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('200');
        return $oDatosCampo;
    }
    private function getDatosIdioma(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('idioma');
        $oDatosCampo->setMetodoGet('getIdiomaVo');
        $oDatosCampo->setMetodoSet('setIdiomaVo');
        $oDatosCampo->setEtiqueta(_("sigla de idioma"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('3');
        return $oDatosCampo;
    }
     private function getDatosNom_idioma(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_idioma');
        $oDatosCampo->setMetodoGet('getNomIdiomaVo');
        $oDatosCampo->setMetodoSet('setNomIdiomaVo');
        $oDatosCampo->setEtiqueta(_("nombre de idioma"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('200');
        return $oDatosCampo;
    }
    private function getDatosActive(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('active');
        $oDatosCampo->setMetodoGet('isActive');
        $oDatosCampo->setMetodoSet('setActive');
        $oDatosCampo->setEtiqueta(_("en activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}
