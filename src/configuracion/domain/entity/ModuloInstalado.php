<?php

namespace src\configuracion\domain\entity;

use core\DatosCampo;
use core\Set;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\value_objects\ModuloId;
use function core\is_true;

/**
 * Clase que implementa la entidad m0_mods_installed_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class ModuloInstalado
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_mod de ModuloInstalado
     *
     * @var int
     */
    private ModuloId $idMod;
    private int $iid_mod;
    /**
     * Status de ModuloInstalado
     *
     * @var bool|null
     */
    private bool|null $bstatus = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ModuloInstalado
     */
    public function setAllAttributes(array $aDatos): ModuloInstalado
    {
        if (array_key_exists('id_mod', $aDatos)) {
            $this->setIdModVo(isset($aDatos['id_mod']) ? new ModuloId((int)$aDatos['id_mod']) : null);
        }
        if (array_key_exists('status', $aDatos)) {
            $this->setStatus(is_true($aDatos['status']));
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

    /**
     *
     * @return bool|null $bstatus
     */
    public function isStatus(): ?bool
    {
        return $this->bstatus;
    }

    /**
     *
     * @param bool|null $bstatus
     */
    public function setStatus(?bool $bstatus = null): void
    {
        $this->bstatus = $bstatus;
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
    function getDatosCampos()
    {
        $oModuloInstaladoSet = new Set();

        $oModuloInstaladoSet->add($this->getDatosId_mod());
        $oModuloInstaladoSet->add($this->getDatosStatus());
        return $oModuloInstaladoSet->getTot();
    }

    function getDatosId_mod()
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
     * Recupera les propietats de l'atribut bstatus de ModuloInstalado
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
        $oDatosCampo->setEtiqueta(_("activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}