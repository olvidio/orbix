<?php

namespace src\usuarios\domain\entity;

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
}
