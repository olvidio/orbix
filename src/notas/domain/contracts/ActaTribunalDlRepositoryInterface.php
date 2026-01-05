<?php

namespace src\notas\domain\contracts;

/**
 * Interfaz de la clase ActaTribunalDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
interface ActaTribunalDlRepositoryInterface extends ActaTribunalRepositoryInterface
{

    public function getNewId();
}