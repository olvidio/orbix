<?php

namespace src\configuracion\domain\entity;

use core\DatosCampo;
use core\Set;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\value_objects\ModuloId;
use src\shared\domain\traits\Hydratable;
use function core\is_true;

class ModuloInstalado
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_mod;
    private bool|null $active = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getIdModVo(): ModuloId
    {
        return new ModuloId($this->id_mod);
    }

    public function setIdModVo(ModuloId $id): void
    {
        $this->id_mod = $id->value();
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_mod(): int
    {
        return $this->id_mod;
    }

    public function setId_mod(int $id_mod): void
    {
        $this->id_mod = $id_mod;
    }


    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active = null): void
    {
        $this->active = $active;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_mod';
    }
    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    public function getDatosCampos(): array
    {
        $oModuloInstaladoSet = new Set();

        $oModuloInstaladoSet->add($this->getDatosId_mod());
        $oModuloInstaladoSet->add($this->getDatosStatus());
        return $oModuloInstaladoSet->getTot();
    }

    private function getDatosId_mod(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_mod');
        $oDatosCampo->setMetodoGet('getId_mod');
        $oDatosCampo->setMetodoSet('setId_mod');
        $oDatosCampo->setEtiqueta(_("nombre"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(ModuloRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNom'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayModulos');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo active de ModuloInstalado
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
        $oDatosCampo->setEtiqueta(_("activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}