<?php

namespace src\profesores\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\profesores\domain\entity\ProfesorDirector;


/**
 * Interfaz de la clase ProfesorDirector y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
interface ProfesorDirectorRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<ProfesorDirector>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorDirector $ProfesorDirector): bool;

    public function Guardar(ProfesorDirector $ProfesorDirector): bool;

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
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?ProfesorDirector;

    public function getNewId(): int;
}