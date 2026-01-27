<?php

namespace src\configuracion\domain\entity;

use core\DatosCampo;
use core\Set;
use src\configuracion\domain\value_objects\AppId;
use src\configuracion\domain\value_objects\AppName;
use src\shared\domain\traits\Hydratable;

class App
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private AppId $id_app;

    private AppName $nom;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    // VO API
    public function getIdAppVo(): AppId
    {
        return $this->id_app;
    }

    public function setIdAppVo(AppId|int $id_app): void
    {
        $this->id_app = $id_app instanceof AppId
            ? $id_app
            : AppId::fromNullableInt($id_app);
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_app(): int
    {
        return $this->id_app->value();
    }

    public function setId_app(int $id_app): void
    {
        $this->id_app = AppId::fromNullableInt($id_app);
    }

    // VO API
    public function getNomVo(): AppName
    {
        return $this->nom;
    }

    public function setNomVo(AppName|string|null $nombre_app): void
    {
        $this->nom = $nombre_app instanceof AppName
            ? $nombre_app
            : AppName::fromNullableString($nombre_app);
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getNom(): string
    {
        return $this->nom?->value();
    }

    public function setNom(string $nom): void
    {
        $this->nom = AppName::fromString($nom);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_app';
    }

    /**
     * Devuelve una colección de objetor tipo DatosCampo
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
        $oDatosCampo->setMetodoGet('getNomVo');
        $oDatosCampo->setMetodoSet('setNom'); // en tablaDB, no se pueden usar lo VO.
        $oDatosCampo->setEtiqueta(_("nombre de la aplicación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);

        return $oDatosCampo;
    }
}
