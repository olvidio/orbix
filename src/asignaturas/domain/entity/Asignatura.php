<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\value_objects\{AsignaturaId,
    AsignaturaName,
    AsignaturaShortName,
    AsignaturaTipoId,
    Creditos,
    NivelId,
    SectorId,
    YearText};
use src\shared\domain\traits\Hydratable;

/**
 * Clase que implementa la entidad xa_asignaturas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class Asignatura
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private AsignaturaId $id_asignatura;

    private NivelId $id_nivel;

    private AsignaturaName $nombre_signatura;

    private ?AsignaturaShortName $nombre_corto = null;

    private ?Creditos $creditos = null;

    private ?YearText $year = null;

    private ?SectorId $id_sector = null;

    private bool $active = false;

    private ?AsignaturaTipoId $id_tipo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    // ---------------- VO API -----------------
    public function getIdAsignaturaVo(): AsignaturaId
    {
        return $this->id_asignatura;
    }

    public function setIdAsignaturaVo(AsignaturaId $id): void
    {
        $this->id_asignatura = $id;
    }

    public function getIdNivelVo(): NivelId
    {
        return $this->id_nivel;
    }

    public function setIdNivelVo(NivelId $id): void
    {
        $this->id_nivel = $id;
    }

    public function getNombreAsignaturaVo(): AsignaturaName
    {
        return $this->nombre_signatura;
    }

    public function setNombreAsignaturaVo(AsignaturaName $nombre): void
    {
        $this->nombre_signatura = $nombre;
    }

    public function getNombreCortoVo(): ?AsignaturaShortName
    {
        return $this->nombre_corto;
    }

    public function setNombreCortoVo(?AsignaturaShortName $nombre = null): void
    {
        $this->nombre_corto = $nombre;
    }

    public function getCreditosVo(): ?Creditos
    {
        return $this->creditos;
    }

    public function setCreditosVo(?Creditos $creditos = null): void
    {
        $this->creditos = $creditos;
    }

    public function getYearVo(): ?YearText
    {
        return $this->year;
    }

    public function setYearVo(?YearText $year = null): void
    {
        $this->year = $year;
    }

    public function getIdSectorVo(): ?SectorId
    {
        return $this->id_sector;
    }

    public function setIdSectorVo(?SectorId $id = null): void
    {
        $this->id_sector = $id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getIdTipoVo(): ?AsignaturaTipoId
    {
        return $this->id_tipo;
    }

    public function setIdTipoVo(?AsignaturaTipoId $id = null): void
    {
        $this->id_tipo = $id;
    }

    // ---------------- LEGACY -----------------

    public function getId_asignatura(): int
    {
        return $this->id_asignatura->value();
    }


    public function setId_asignatura(int $id_asignatura): void
    {
        $this->id_asignatura = new AsignaturaId($id_asignatura);
    }


    public function getId_nivel(): int
    {
        return $this->id_nivel->value();
    }


    public function setId_nivel(int $id_nivel): void
    {
        $this->id_nivel = $id_nivel !== null ? new NivelId($id_nivel) : null;
    }


    public function getNombre_signatura(): string
    {
        return $this->nombre_signatura->value();
    }


    public function setNombre_signatura(string $nombre_asignatura): void
    {
        $this->nombre_signatura = new AsignaturaName($nombre_asignatura);
    }


    public function getNombre_corto(): ?string
    {
        return $this->nombre_corto?->value();
    }


    public function setNombre_corto(?string $nombre_corto = null): void
    {
        $this->nombre_corto = AsignaturaShortName::fromNullableString($nombre_corto);
    }


    public function getCreditos(): ?float
    {
        return $this->creditos?->value();
    }


    public function setCreditos(?float $creditos = null): void
    {
        $this->creditos = Creditos::fromNullable($creditos);
    }


    public function getYear(): ?string
    {
        return $this->year?->value();
    }


    public function setYear(?string $year = null): void
    {
        $this->year = YearText::fromNullableString($year);
    }


    public function getId_sector(): ?int
    {
        return $this->id_sector?->value();
    }


    public function setId_sector(?int $id_sector = null): void
    {
        $this->id_sector = $id_sector !== null ? new SectorId($id_sector) : null;
    }

    public function getId_tipo(): ?int
    {
        return $this->id_tipo?->value();
    }


    public function setId_tipo(?int $id_tipo = null): void
    {
        $this->id_tipo = $id_tipo !== null ? new AsignaturaTipoId($id_tipo) : null;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_asignatura';
    }

  public function getDatosCampos(): array
    {
        $oAsignaturaSet = new Set();

        $oAsignaturaSet->add($this->getDatosId_asignatura());
        $oAsignaturaSet->add($this->getDatosId_nivel());
        $oAsignaturaSet->add($this->getDatosNombre_asignatura());
        $oAsignaturaSet->add($this->getDatosNombre_corto());
        $oAsignaturaSet->add($this->getDatosCreditos());
        $oAsignaturaSet->add($this->getDatosYear());
        $oAsignaturaSet->add($this->getDatosId_sector());
        $oAsignaturaSet->add($this->getDatosStatus());
        $oAsignaturaSet->add($this->getDatosId_tipo());
        return $oAsignaturaSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo id_asignatura de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_asignatura(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_asignatura');
        $oDatosCampo->setMetodoGet('getId_asignatura');
        $oDatosCampo->setMetodoSet('setId_asignatura');
        $oDatosCampo->setEtiqueta(_("id asignatura"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo id_nivel de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_nivel(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nivel');
        $oDatosCampo->setMetodoGet('getId_nivel');
        $oDatosCampo->setMetodoSet('setId_nivel');
        $oDatosCampo->setEtiqueta(_("id nivel"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo nombre_asignatura de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_asignatura(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_asignatura');
        $oDatosCampo->setMetodoGet('getNombre_asignatura');
        $oDatosCampo->setMetodoSet('setNombre_asignatura');
        $oDatosCampo->setEtiqueta(_("nombre largo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(100);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo nombre_corto de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_corto(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_corto');
        $oDatosCampo->setMetodoGet('getNombre_corto');
        $oDatosCampo->setMetodoSet('setNombre_corto');
        $oDatosCampo->setEtiqueta(_("nombre corto"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(23);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo creditos de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosCreditos(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('creditos');
        $oDatosCampo->setMetodoGet('getCreditos');
        $oDatosCampo->setMetodoSet('setCreditos');
        $oDatosCampo->setEtiqueta(_("créditos"));
        $oDatosCampo->setTipo('decimal');
        $oDatosCampo->setArgument(4);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo year de Asignatura
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
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(4);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo id_sector de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_sector(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_sector');
        $oDatosCampo->setMetodoGet('getId_sector');
        $oDatosCampo->setMetodoSet('setId_sector');
        $oDatosCampo->setEtiqueta(_("sector"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(SectorRepositoryInterface::class);
        $oDatosCampo->setArgument2('getSector'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArraySectores');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo active de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosStatus(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('active');
        $oDatosCampo->setMetodoGet('isActive');
        $oDatosCampo->setMetodoSet('setActive');
        $oDatosCampo->setEtiqueta(_("en uso"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo id_tipo de Asignatura
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_tipo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo');
        $oDatosCampo->setMetodoGet('getId_tipo');
        $oDatosCampo->setMetodoSet('setId_tipo');
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(AsignaturaTipoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getTipo_asignatura'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayAsignaturaTipos');
        return $oDatosCampo;
    }
}