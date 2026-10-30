<?php

namespace src\usuarios\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\usuarios\domain\entity\Preferencia;

/**
 * Interfaz de la clase Preferencia y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface PreferenciaRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<Preferencia>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Preferencia $Preferencia): bool;

    public function Guardar(Preferencia $Preferencia): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $tipo
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_usuario, string $tipo): array|false;

    /**
     * Busca la clase con tipo en el repositorio.
     */
    public function findById(int $id_usuario, string $tipo): ?Preferencia;
}