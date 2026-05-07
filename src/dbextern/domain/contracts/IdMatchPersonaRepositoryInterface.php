<?php

namespace src\dbextern\domain\contracts;

use src\dbextern\domain\entity\IdMatchPersona;

/**
 * Interfaz del repositorio de IdMatchPersona
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 7/5/2026
 */
interface IdMatchPersonaRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    public function getIdMatchPersonas(array $aWhere = [], array $aOperators = []): array|bool;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(IdMatchPersona $IdMatchPersona): bool;

    public function Guardar(IdMatchPersona $IdMatchPersona): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    public function datosById(int $id_listas): array|bool;

    public function findById(int $id_listas): ?IdMatchPersona;
}
