<?php

namespace src\actividades\domain\entity;
/**
 * Clase que implementa la entidad a_importadas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class Importada
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_activ de Importada
     *
     * @var int
     */
    private int $iid_activ;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Importada
     */
    public function setAllAttributes(array $aDatos): Importada
    {
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_activ
     */
    public function getId_activ(): int
    {
        return $this->iid_activ;
    }

    /**
     *
     * @param int $iid_activ
     */
    public function setId_activ(int $iid_activ): void
    {
        $this->iid_activ = $iid_activ;
    }
}