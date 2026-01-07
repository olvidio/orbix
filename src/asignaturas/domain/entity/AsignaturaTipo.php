<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\value_objects\{AsignaturaTipoId,
    AsignaturaTipoLatin,
    AsignaturaTipoName,
    AsignaturaTipoShortName,
    AsignaturaTipoYear};
use src\shared\domain\traits\Hydratable;

class AsignaturaTipo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private AsignaturaTipoId $id_tipo;

    private AsignaturaTipoName $tipo_asignatura;

    private AsignaturaTipoShortName $tipo_breve;

    private ?AsignaturaTipoYear $year = null;

    private ?AsignaturaTipoLatin $tipo_latin = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // ---------- VO API ----------
    public function getIdTipoVo(): AsignaturaTipoId
    {
        return $this->id_tipo;
    }

    public function setIdTipoVo(AsignaturaTipoId|int $id): void
    {
        $this->id_tipo = $id instanceof AsignaturaTipoId
            ? $id
            : AsignaturaTipoId::fromNullable($id);
    }

    public function getTipoAsignaturaVo(): AsignaturaTipoName
    {
        return $this->tipo_asignatura;
    }

    public function setTipoAsignaturaVo(AsignaturaTipoName|string $nombre): void
    {
        $this->tipo_asignatura = $nombre instanceof AsignaturaTipoName
            ? $nombre
            : AsignaturaTipoName::fromNullableString($nombre);
    }

    public function getTipoBreveVo(): AsignaturaTipoShortName
    {
        return $this->tipo_breve;
    }

    public function setTipoBreveVo(AsignaturaTipoShortName|string $nombre): void
    {
        $this->tipo_breve = $nombre instanceof AsignaturaTipoShortName
            ? $nombre
            : AsignaturaTipoShortName::fromNullableString($nombre);
    }

    public function getYearVo(): ?AsignaturaTipoYear
    {
        return $this->year;
    }

    public function setYearVo(AsignaturaTipoYear|string|null $texto = null): void
    {
        $this->year = $texto instanceof AsignaturaTipoYear
            ? $texto
            : AsignaturaTipoYear::fromNullableString($texto);
    }

    public function getTipoLatinVo(): ?AsignaturaTipoLatin
    {
        return $this->tipo_latin;
    }

    public function setTipoLatinVo(AsignaturaTipoLatin|string|null $texto = null): void
    {
        $this->tipo_latin = $texto instanceof AsignaturaTipoLatin
            ? $texto
            : AsignaturaTipoLatin::fromNullableString($texto);
    }

    // ---------- LEGACY ----------

    public function getId_tipo(): int
    {
        return $this->id_tipo->value();
    }


    public function setId_tipo(int $id_tipo): void
    {
        $this->id_tipo = AsignaturaTipoId::fromNullable($id_tipo);
    }


    public function getTipo_asignatura(): string
    {
        return $this->tipo_asignatura->value();
    }


    public function setTipo_asignatura(string $tipo_asignatura): void
    {
        $this->tipo_asignatura = AsignaturaTipoName::fromNullableString($tipo_asignatura);
    }


    public function getTipo_breve(): string
    {
        return $this->tipo_breve->value();
    }


    public function setTipo_breve(string $tipo_breve): void
    {
        $this->tipo_breve = AsignaturaTipoShortName::fromNullableString($tipo_breve);
    }


    public function getYear(): ?string
    {
        return $this->year?->value();
    }


    public function setYear(?string $year = null): void
    {
        $this->year = AsignaturaTipoYear::fromNullableString($year);
    }


    public function getTipo_latin(): ?string
    {
        return $this->tipo_latin?->value();
    }


    public function setTipo_latin(?string $tipo_latin = null): void
    {
        $this->tipo_latin = AsignaturaTipoLatin::fromNullableString($tipo_latin);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo';
    }

    public function getDatosCampos(): array
    {
        $oAsignaturaTipoSet = new Set();

        $oAsignaturaTipoSet->add($this->getDatosTipo_asignatura());
        $oAsignaturaTipoSet->add($this->getDatosTipo_breve());
        $oAsignaturaTipoSet->add($this->getDatosYear());
        $oAsignaturaTipoSet->add($this->getDatosTipo_latin());
        return $oAsignaturaTipoSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo tipo_asignatura de AsignaturaTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo_asignatura(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_asignatura');
        $oDatosCampo->setMetodoGet('getTipo_asignatura');
        $oDatosCampo->setMetodoSet('setTipo_asignatura');
        $oDatosCampo->setEtiqueta(_("tipo de asignatura"));
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo tipo_breve de AsignaturaTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo_breve(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_breve');
        $oDatosCampo->setMetodoGet('getTipo_breve');
        $oDatosCampo->setMetodoSet('setTipo_breve');
        $oDatosCampo->setEtiqueta(_("tipo breve"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut saño de AsignaturaTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosYear(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('year');
        $oDatosCampo->setMetodoGet('getYear');
        $oDatosCampo->setMetodoSet('setYear');
        $oDatosCampo->setEtiqueta(_("año"));
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo tipo_latin de AsignaturaTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo_latin(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_latin');
        $oDatosCampo->setMetodoGet('getTipo_latin');
        $oDatosCampo->setMetodoSet('setTipo_latin');
        $oDatosCampo->setEtiqueta(_("tipo_latin"));
        return $oDatosCampo;
    }
}