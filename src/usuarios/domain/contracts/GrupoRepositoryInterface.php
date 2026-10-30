<?php

namespace src\usuarios\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\usuarios\domain\entity\Grupo;

/**
 * Interfaz de la clase Grupo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface GrupoRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<Grupo>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Grupo $Grupo): bool;

    public function Guardar(Grupo $Grupo): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_usuario
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_usuario): array|false;

    /**
     * Busca la clase con id_usuario en el repositorio.
     */
    public function findById(int $id_usuario): ?Grupo;

    public function getNewId(): int;
}