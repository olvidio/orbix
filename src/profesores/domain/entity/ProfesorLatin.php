<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Clase que implementa la entidad d_profesor_latin
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorLatin
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_nom de ProfesorLatin
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Latin de ProfesorLatin
     *
     * @var bool
     */
    private bool $blatin;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorLatin
     */
    public function setAllAttributes(array $aDatos): ProfesorLatin
    {
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('latin', $aDatos)) {
            $this->setLatin(is_true($aDatos['latin']));
        }
        return $this;
    }

    /**
     *
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     *
     * @return bool $blatin
     */
    public function isLatin(): bool
    {
        return $this->blatin;
    }

    /**
     *
     * @param bool $blatin
     */
    public function setLatin(bool $blatin): void
    {
        $this->blatin = $blatin;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_nom';
    }

    function getDatosCampos(): array
    {
        $oProfesorLatinSet = new Set();

        $oProfesorLatinSet->add($this->getDatosLatin());
        return $oProfesorLatinSet->getTot();
    }

    function getDatosLatin(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('latin');
        $oDatosCampo->setMetodoGet('isLatin');
        $oDatosCampo->setMetodoSet('setLatin');
        $oDatosCampo->setEtiqueta(_("latín"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}