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


    private int $id_auto;
    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_auto(): int
    {
        return $this->id_auto;
    }


    public function setId_auto(int $id_auto): void
    {
        $this->id_auto = $id_auto;
    }

}