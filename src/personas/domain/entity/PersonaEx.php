<?php

namespace src\personas\domain\entity;

use function core\is_true;

/**
 * Clase que implementa la entidad p_numerarios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PersonaEx extends PersonaPub
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_auto de PersonaN
     *
     * @var int
     */
    private int $iid_auto;
    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     *
     * @return int $iid_auto
     */
    public function getId_auto(): int
    {
        return $this->iid_auto;
    }

    /**
     *
     * @param int $iid_auto
     */
    public function setId_auto(int $iid_auto): void
    {
        $this->iid_auto = $iid_auto;
    }

}