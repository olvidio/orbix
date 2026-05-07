<?php

namespace src\dbextern\domain\contracts;

use src\dbextern\domain\entity\PersonaBDU;

/**
 * Interfaz del repositorio de PersonaBDU
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 7/5/2026
 */
interface PersonaBDURepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    public function getPersonaBDUQuery(string $sQuery = ''): array;

    public function getIdMatchPersonas(array $aWhere = [], array $aOperators = []): array|bool;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    public function datosById(int $id_listas): array|bool;

    public function findById(int $id_listas): ?PersonaBDU;
}
