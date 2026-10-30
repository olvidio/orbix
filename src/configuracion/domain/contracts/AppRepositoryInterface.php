<?php

namespace src\configuracion\domain\contracts;

use src\configuracion\domain\entity\App;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase App y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 10/11/2025
 */
interface AppRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Apps que cumplen el criterio.
     *
     * @return list<App>
     */
    public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(App $App): bool;

    public function Guardar(App $App): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;
    
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_app
     * @return array<string, mixed>|false
    
     */
    public function datosById(int $id_app): array|false;
    
    /**
     * Busca la clase con id_app en el repositorio.
    
     */
    public function findById(int $id_app): ?App;
    
    /**
     * @return int|string
     */
    /**
     * @return int|string
     */
    public function getNewId();
}
