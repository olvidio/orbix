<?php

namespace src\configuracion\domain\entity;

use core\DatosCampo;
use core\Set;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;
use src\configuracion\domain\value_objects\ModsReq;
use src\shared\domain\traits\Hydratable;


class Modulo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private ModuloId $id_mod;

    private ModuloName $nom;

    private ?ModuloDescription $descripcion = null;

    private ?ModsReq $mods_req = null;

    private ?AppsReq $apps_req = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getIdModVo(): ModuloId
    {
        return $this->id_mod;
    }

    public function setIdModVo(ModuloId $id): void
    {
        $this->id_mod = $id;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_mod(): int
    {
        return $this->id_mod->value();
    }

    public function setId_mod(int $id_mod): void
    {
        $this->id_mod = new ModuloId($id_mod);
    }

    // VO API
    public function getNomVo(): ModuloName
    {
        return $this->nom;
    }

    public function setNomVo(ModuloName $nombre): void
    {
        $this->nom = $nombre;
    }

    // Legacy scalar API
    public function getNom(): string
    {
        return $this->nom->value();
    }

    public function setNom(string $nom): void
    {
        $this->nom = ModuloName::fromString($nom);
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

    public function setDescripcion(?string $descripcion = null): void
    {
        $this->descripcion = ModuloDescription::fromNullableString($descripcion);
    }

    // VO API
    public function getModsReqVo(): ?ModsReq
    {
        return $this->mods_req;
    }

    public function setModsReqVo(?ModsReq $mods = null): void
    {
        $this->mods_req = $mods;
    }

    // Legacy scalar API
    public function getMods_req(): ?array
    {
        return $this->mods_req?->toArray();
    }

    public function setMods_req(?array $a_mods_req = null): void
    {
        $this->mods_req = ModsReq::fromNullableArray($a_mods_req);
    }

    // VO API
    public function getAppsReqVo(): ?AppsReq
    {
        return $this->apps_req;
    }

    public function setAppsReqVo(?AppsReq $apps = null): void
    {
        $this->apps_req = $apps;
    }

    // Legacy scalar API
    public function getApps_req(): ?array
    {
        return $this->apps_req?->toArray();
    }

    public function setApps_req(?array $a_apps_req = null): void
    {
        $this->apps_req = AppsReq::fromNullableArray($a_apps_req);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_mod';
    }

    public function getDatosCampos(): array
    {
        $oModuloSet = new Set();

        $oModuloSet->add($this->getDatosNom());
        $oModuloSet->add($this->getDatosDescripcion());
        $oModuloSet->add($this->getDatosMods_req());
        $oModuloSet->add($this->getDatosApps_req());
        return $oModuloSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo nom de Modulo en DatosCampo
     */
    private function getDatosNom(): DatosCampo
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
     * Recupera las propiedades del atributo descripcion de Modulo en DatosCampo
     */
    private function getDatosDescripcion(): DatosCampo
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
     * Recupera las propiedades del atributo mods_req de Modulo en DatosCampo
     */
    private function getDatosMods_req(): DatosCampo
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
     * Recupera las propiedades del atributo apps_req de Modulo en DatosCampo
     */
    private function getDatosApps_req(): DatosCampo
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