<?php

namespace src\personas\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\personas\domain\entity\TelecoPersona;


/**
 * Interfaz de la clase TelecoPersona y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/12/2025
 */
interface TelecoPersonaRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<TelecoPersona>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TelecoPersona $TelecoPersona): bool;

    public function Guardar(TelecoPersona $TelecoPersona): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_item): ?TelecoPersona;
}
