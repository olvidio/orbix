<?php

namespace src\configuracion\domain\entity;

use core\DatosCampo;
use core\Set;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;
use src\configuracion\domain\value_objects\ModsReq;

/**
 * Clase que implementa la entidad m0_modulos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 11/11/2025
 */
class Modulo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id del Módulo
     */
    private ModuloId $idMod;
    /**
     * Nombre del Módulo
     */
    private ModuloName $nombreMod;
    /**
     * Descripción del Módulo
     */
    private ?ModuloDescription $descripcion = null;
    /**
     * Módulos requeridos
     */
    private ?ModsReq $modsReq = null;
    /**
     * Apps requeridas
     */
    private ?AppsReq $appsReq = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Modulo
     */
    public function setAllAttributes(array $aDatos): Modulo
    {
        if (array_key_exists('id_mod', $aDatos)) {
            $this->setIdModVo(isset($aDatos['id_mod']) ? new ModuloId((int)$aDatos['id_mod']) : null);
        }
        if (array_key_exists('nom', $aDatos)) {
            $this->setNombreModVo(ModuloName::fromString($aDatos['nom'] ?? null));
        }
        if (array_key_exists('descripcion', $aDatos)) {
            $this->setDescripcionVo(ModuloDescription::fromNullableString($aDatos['descripcion'] ?? null));
        }
        if (array_key_exists('mods_req', $aDatos)) {
            $this->setModsReqVo(ModsReq::fromNullableArray($aDatos['mods_req'] ?? null));
        }
        if (array_key_exists('apps_req', $aDatos)) {
            $this->setAppsReqVo(AppsReq::fromNullableArray($aDatos['apps_req'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getIdModVo(): ModuloId
    {
        return $this->idMod;
    }

    public function setIdModVo(ModuloId $id): void
    {
        $this->idMod = $id;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_mod(): int
    {
        return $this->idMod->value();
    }

    public function setId_mod(int $iid_mod): void
    {
        $this->idMod = new ModuloId($iid_mod);
    }

    // VO API
    public function getNombreModVo(): ModuloName
    {
        return $this->nombreMod;
    }

    public function setNombreModVo(ModuloName $nombre): void
    {
        $this->nombreMod = $nombre;
    }

    // Legacy scalar API
    public function getNombreMod(): string
    {
        return $this->nombreMod->value();
    }

    public function setNombreMod(string $nombre): void
    {
        $this->nombreMod = ModuloName::fromString($nombre);
    }

    // Aliases for historical getNom/setNom
    public function getNom(): string
    {
        return $this->getNombreMod();
    }

    public function setNom(string $snom): void
    {
        $this->setNombreMod($snom);
    }

    // VO API
    public function getDescripcionVo(): ?ModuloDescription
    {
        return $this->descripcion;
    }

    public function setDescripcionVo(?ModuloDescription $desc = null): void
    {
        $this->descripcion = $desc;
    }

    // Legacy scalar API
    public function getDescripcion(): ?string
    {
        return $this->descripcion?->value();
    }

    public function setDescripcion(?string $sdescripcion = null): void
    {
        $this->descripcion = ModuloDescription::fromNullableString($sdescripcion);
    }

    // VO API
    public function getModsReqVo(): ?ModsReq
    {
        return $this->modsReq;
    }

    public function setModsReqVo(?ModsReq $mods = null): void
    {
        $this->modsReq = $mods;
    }

    // Legacy scalar API
    public function getMods_req(): ?array
    {
        return $this->modsReq?->toArray();
    }

    public function setMods_req(?array $a_mods_req = null): void
    {
        $this->modsReq = ModsReq::fromNullableArray($a_mods_req);
    }

    // VO API
    public function getAppsReqVo(): ?AppsReq
    {
        return $this->appsReq;
    }

    public function setAppsReqVo(?AppsReq $apps = null): void
    {
        $this->appsReq = $apps;
    }

    // Legacy scalar API
    public function getApps_req(): ?array
    {
        return $this->appsReq?->toArray();
    }

    public function setApps_req(?array $a_apps_req = null): void
    {
        $this->appsReq = AppsReq::fromNullableArray($a_apps_req);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_mod';
    }

    public function getDatosCampos()
    {
        $oModuloSet = new Set();

        $oModuloSet->add($this->getDatosNom());
        $oModuloSet->add($this->getDatosDescripcion());
        $oModuloSet->add($this->getDatosMods_req());
        $oModuloSet->add($this->getDatosApps_req());
        return $oModuloSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut snom de Modulo en DatosCampo
     */
    public function getDatosNom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom');
        $oDatosCampo->setMetodoGet('getNombreModVo');
        $oDatosCampo->setMetodoSet('setNombreMod'); // en tablaDB, no se pueden usar lo VO.
        $oDatosCampo->setEtiqueta(_("nombre del módulo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdescripcion de Modulo en DatosCampo
     */
    public function getDatosDescripcion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('descripcion');
        $oDatosCampo->setMetodoGet('getDescripcionVo');
        $oDatosCampo->setMetodoSet('setDescripcion'); // en tablaDB, no se pueden usar lo VO.
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut smods_req de Modulo en DatosCampo
     */
    public function getDatosMods_req(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('mods_req');
        $oDatosCampo->setMetodoGet('getModsReqVo');
        $oDatosCampo->setMetodoSet('setMods_req'); // en tablaDB, no se pueden usar lo VO.
        $oDatosCampo->setEtiqueta(_("mods requeridos"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sapps_req de Modulo en DatosCampo
     */
    public function getDatosApps_req(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('apps_req');
        $oDatosCampo->setMetodoGet('getAppsReqVo');
        $oDatosCampo->setMetodoSet('setApps_req'); // en tablaDB, no se pueden usar lo VO.
        $oDatosCampo->setEtiqueta(_("apps requeridas"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}