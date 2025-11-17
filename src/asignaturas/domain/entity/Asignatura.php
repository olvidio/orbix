<?php

namespace src\asignaturas\domain\entity;
use core\DatosCampo;
use core\Set;
use function core\is_true;
use src\asignaturas\domain\value_objects\{AsignaturaId, NivelId, AsignaturaName, AsignaturaShortName, Creditos, YearText, AsignaturaTipoId, SectorId};
/**
 * Clase que implementa la entidad xa_asignaturas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class Asignatura {

	/* ATRIBUTOS ----------------------------------------------------------------- */

 /**
  * Id_asignatura de Asignatura
  */
 private AsignaturaId $idAsignatura;
 /**
  * Id_nivel de Asignatura
  */
 private NivelId $idNivel;
 /**
  * Nombre_asignatura de Asignatura
  */
 private AsignaturaName $nombreAsignatura;
 /**
  * Nombre_corto de Asignatura
  */
 private ?AsignaturaShortName $nombreCorto = null;
 /**
  * Creditos de Asignatura
  */
 private ?Creditos $creditos = null;
 /**
  * Year de Asignatura
  */
 private ?YearText $year = null;
 /**
  * Id_sector de Asignatura (FK)
  */
 private ?SectorId $idSector = null;
 /**
  * Status de Asignatura
  */
 private bool $status = false;
 /**
  * Id_tipo de Asignatura
  */
 private ?AsignaturaTipoId $idTipo = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Asignatura
	 */
 public function setAllAttributes(array $aDatos): Asignatura
 {
     if (array_key_exists('id_asignatura', $aDatos)) {
         $this->setIdAsignaturaVo(new AsignaturaId((int)$aDatos['id_asignatura']));
     }
     if (array_key_exists('id_nivel', $aDatos)) {
         $valor = $aDatos['id_nivel'] ?? null;
         $this->setIdNivelVo(isset($valor) && $valor !== '' ? new NivelId((int)$valor) : null);
     }
     if (array_key_exists('nombre_asignatura', $aDatos)) {
         $this->setNombreAsignaturaVo(new AsignaturaName((string)$aDatos['nombre_asignatura']));
     }
     if (array_key_exists('nombre_corto', $aDatos)) {
         $this->setNombreCortoVo(AsignaturaShortName::fromNullableString($aDatos['nombre_corto'] ?? null));
     }
     if (array_key_exists('creditos', $aDatos)) {
         $valor = $aDatos['creditos'] ?? null;
         $this->setCreditosVo($valor !== null && $valor !== '' ? new Creditos((float)$valor) : null);
     }
     if (array_key_exists('year', $aDatos)) {
         $this->setYearVo(YearText::fromNullableString($aDatos['year'] ?? null));
     }
     if (array_key_exists('id_sector', $aDatos)) {
         $valor = $aDatos['id_sector'] ?? null;
         $this->setIdSectorVo(isset($valor) && $valor !== '' ? new SectorId((int)$valor) : null);
     }
     if (array_key_exists('status', $aDatos)) {
         $this->setStatus(is_true($aDatos['status']));
     }
     if (array_key_exists('id_tipo', $aDatos)) {
         $valor = $aDatos['id_tipo'] ?? null;
         $this->setIdTipoVo(isset($valor) && $valor !== '' ? new AsignaturaTipoId((int)$valor) : null);
     }
     return $this;
 }

 // ---------------- VO API -----------------
 public function getIdAsignaturaVo(): AsignaturaId { return $this->idAsignatura; }
 public function setIdAsignaturaVo(AsignaturaId $id): void { $this->idAsignatura = $id; }

 public function getIdNivelVo(): NivelId { return $this->idNivel; }
 public function setIdNivelVo(NivelId $id): void { $this->idNivel = $id; }

 public function getNombreAsignaturaVo(): AsignaturaName { return $this->nombreAsignatura; }
 public function setNombreAsignaturaVo(AsignaturaName $nombre): void { $this->nombreAsignatura = $nombre; }

 public function getNombreCortoVo(): ?AsignaturaShortName { return $this->nombreCorto; }
 public function setNombreCortoVo(?AsignaturaShortName $nombre = null): void { $this->nombreCorto = $nombre; }

 public function getCreditosVo(): ?Creditos { return $this->creditos; }
 public function setCreditosVo(?Creditos $creditos = null): void { $this->creditos = $creditos; }

 public function getYearVo(): ?YearText { return $this->year; }
 public function setYearVo(?YearText $year = null): void { $this->year = $year; }

 public function getIdSectorVo(): ?SectorId { return $this->idSector; }
 public function setIdSectorVo(?SectorId $id = null): void { $this->idSector = $id; }

 public function getIdTipoVo(): ?AsignaturaTipoId { return $this->idTipo; }
 public function setIdTipoVo(?AsignaturaTipoId $id = null): void { $this->idTipo = $id; }
	/**
	 *
	 * @return int $iid_asignatura
	 */
 public function getId_asignatura(): int
 {
     return $this->idAsignatura->value();
 }
	/**
	 *
	 * @param int $iid_asignatura
	 */
 public function setId_asignatura(int $iid_asignatura): void
 {
     $this->idAsignatura = new AsignaturaId($iid_asignatura);
 }
	/**
	 *
	 * @return int|null $iid_nivel
	 */
 public function getId_nivel(): int
 {
     return $this->idNivel->value();
 }
	/**
	 *
	 * @param int|null $iid_nivel
	 */
 public function setId_nivel(int $iid_nivel): void
 {
     $this->idNivel = $iid_nivel !== null ? new NivelId($iid_nivel) : null;
 }
	/**
	 *
	 * @return string $snombre_asignatura
	 */
 public function getNombre_asignatura(): string
 {
     return $this->nombreAsignatura->value();
 }
	/**
	 *
	 * @param string $snombre_asignatura
	 */
 public function setNombre_asignatura(string $snombre_asignatura): void
 {
     $this->nombreAsignatura = new AsignaturaName($snombre_asignatura);
 }
	/**
	 *
	 * @return string|null $snombre_corto
	 */
 public function getNombre_corto(): ?string
 {
     return $this->nombreCorto?->value();
 }
	/**
	 *
	 * @param string|null $snombre_corto
	 */
 public function setNombre_corto(?string $snombre_corto = null): void
 {
     $this->nombreCorto = AsignaturaShortName::fromNullableString($snombre_corto);
 }
	/**
	 *
	 * @return float|null $icreditos
	 */
 public function getCreditos(): ?float
 {
     return $this->creditos?->value();
 }
	/**
	 *
	 * @param float|null $icreditos
	 */
 public function setCreditos(?float $icreditos = null): void
 {
     $this->creditos = Creditos::fromNullable($icreditos);
 }
	/**
	 *
	 * @return string|null $syear
	 */
 public function getYear(): ?string
 {
     return $this->year?->value();
 }
	/**
	 *
	 * @param string|null $syear
	 */
 public function setYear(?string $syear = null): void
 {
     $this->year = YearText::fromNullableString($syear);
 }
	/**
	 *
	 * @return int|null $iid_sector
	 */
 public function getId_sector(): ?int
 {
     return $this->idSector?->value();
 }
	/**
	 *
	 * @param int|null $iid_sector
	 */
 public function setId_sector(?int $iid_sector = null): void
 {
     $this->idSector = $iid_sector !== null ? new SectorId($iid_sector) : null;
 }
	/**
	 *
	 * @return bool $bstatus
	 */
 public function isStatus(): bool
 {
     return $this->status;
 }
	/**
	 *
	 * @param bool $bstatus
	 */
 public function setStatus(bool $bstatus): void
 {
     $this->status = $bstatus;
 }
	/**
	 *
	 * @return int|null $iid_tipo
	 */
 public function getId_tipo(): ?int
 {
     return $this->idTipo?->value();
 }
	/**
	 *
	 * @param int|null $iid_tipo
	 */
 public function setId_tipo(?int $iid_tipo = null): void
 {
     $this->idTipo = $iid_tipo !== null ? new AsignaturaTipoId($iid_tipo) : null;
 }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_asignatura';
    }

    function getDatosCampos()
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
     * Recupera les propietats de l'atribut iid_asignatura de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_asignatura()
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
     * Recupera les propietats de l'atribut iid_nivel de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_nivel()
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
     * Recupera les propietats de l'atribut snombre_asignatura de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_asignatura()
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
     * Recupera les propietats de l'atribut snombre_corto de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_corto()
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
     * Recupera les propietats de l'atribut screditos de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCreditos()
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
     * Recupera les propietats de l'atribut syear de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosYear()
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
     * Recupera les propietats de l'atribut iid_sector de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_sector()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_sector');
        $oDatosCampo->setMetodoGet('getId_sector');
        $oDatosCampo->setMetodoSet('setId_sector');
        $oDatosCampo->setEtiqueta(_("sector"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\asignaturas\\application\\repositories\\SectorRepository');
        $oDatosCampo->setArgument2('getSector'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArraySectores');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bstatus de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosStatus()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('status');
        $oDatosCampo->setMetodoGet('isStatus');
        $oDatosCampo->setMetodoSet('setStatus');
        $oDatosCampo->setEtiqueta(_("en uso"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tipo de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tipo()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo');
        $oDatosCampo->setMetodoGet('getId_tipo');
        $oDatosCampo->setMetodoSet('setId_tipo');
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\asignaturas\\application\\repositories\\AsignaturaTipoRepository');
        $oDatosCampo->setArgument2('getTipo_asignatura'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayAsignaturaTipos');
        return $oDatosCampo;
    }
}