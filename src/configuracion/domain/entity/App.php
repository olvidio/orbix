<?php

namespace src\configuracion\domain\entity;
use src\configuracion\domain\value_objects\AppId;
use src\configuracion\domain\value_objects\AppName;
use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;

class App
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private AppId $id_app;

    private AppName $nombre_app;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


       // VO API
    public function getIdAppVo(): AppId
    {
        return $this->id_app;
    }

    public function setIdAppVo(AppId $id_app): void
    {
        $this->id_app = $id_app;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_app(): int
    {
        return $this->id_app->value();
    }

    public function setId_app(int $id_app): void
    {
        $this->id_app = new AppId($id_app);
    }

    // VO API
    public function getNombreAppVo(): AppName
    {
        return $this->nombre_app;
    }

    public function setNombreAppVo(AppName $nombre_app): void
    {
        $this->nombre_app = $nombre_app;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getNombreApp(): string
    {
        return $this->nombre_app?->value();
    }

    public function setNombreApp(string $nombre_app): void
    {
        $this->nombre_app = AppName::fromString($nombre_app);
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
    public function getDatosCampos(): array
    {
        $oAppSet = new Set();

        $oAppSet->add($this->getDatosNombreApp());
        return $oAppSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo nom de App
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombreApp(): DatosCampo
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
