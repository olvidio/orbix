<?php

namespace src\asistentes\domain\contracts;


/**
 * Interfaz de la clase Asistente y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
interface AsistentePubRepositoryInterface extends AsistenteRepositoryInterface
{
    /**
     * @param list<int> $aId_activ
     * @return list<int>|false
     */
    public function getListaAsistentesDistintos(array $aId_activ = []): array|false;
}