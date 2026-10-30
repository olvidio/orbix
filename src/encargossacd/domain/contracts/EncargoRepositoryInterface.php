<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\Encargo;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase Encargo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Encargo que cumplen el criterio.
     *
     * @return list<Encargo>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Encargo $Encargo): bool;

    public function Guardar(Encargo $Encargo): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_enc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_enc): array|false;

    /**
     * Busca la clase con id_enc en el repositorio.
     */
    public function findById(int $id_enc): ?Encargo;

    public function getNewId(): int;
}