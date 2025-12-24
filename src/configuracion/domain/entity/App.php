<?php

namespace src\configuracion\domain\entity;
use src\configuracion\domain\value_objects\AppId;
use src\configuracion\domain\value_objects\AppName;
use core\DatosCampo;
use core\Set;

/**
 * Clase que implementa la entidad m0_apps
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 10/11/2025
 */
class App
{

    /* ATRIBUTOS ----------------------------------------------------------------- */
  /**
     * Id_dl de Delegacion
     */
    private AppId $idApp;
    /**
     * Nombre de la Delegacion
     */
    private AppName $nombreApp;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return App
     */
    public function setAllAttributes(array $aDatos): App
    {
        if (array_key_exists('id_app', $aDatos)) {
            $this->setIdAppVo(isset($aDatos['id_app']) ? new AppId((int)$aDatos['id_app']) : null);
        }
         if (array_key_exists('nom', $aDatos)) {
            $this->setNombreAppVo(AppName::fromString($aDatos['nom'] ?? null));
        }
        return $this;
    }

       // VO API
    public function getIdAppVo(): AppId
    {
        return $this->idApp;
    }

    public function setIdAppVo(AppId $iid_app): void
    {
        $this->idApp = $iid_app;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_app(): int
    {
        return $this->idApp->value();
    }

    public function setId_app(int $iid_app): void
    {
        $this->idApp = new AppId($iid_app);
    }

    // VO API
    public function getNombreAppVo(): AppName
    {
        return $this->nombreApp;
    }

    public function setNombreAppVo(AppName $snombre_app): void
    {
        $this->nombreApp = $snombre_app;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getNombreApp(): string
    {
        return $this->nombreApp?->value();
    }

    public function setNombreApp(string $snombre_app): void
    {
        $this->nombreApp = AppName::fromString($snombre_app);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_app';
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oAppSet = new Set();

        $oAppSet->add($this->getDatosNombreApp());
        return $oAppSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut snom de App
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombreApp()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom');
        $oDatosCampo->setMetodoGet('getNombreAppVo');
        $oDatosCampo->setMetodoSet('setNombreApp'); // en tablaDB, no se pueden usar lo VO.
        $oDatosCampo->setEtiqueta(_("nombre de la aplicación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);

        return $oDatosCampo;
    }
}
