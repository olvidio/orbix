<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Region;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Region y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
interface RegionRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Region que cumplen el criterio.
     *
     * @return list<Region>
     */
    public function matching(Criteria $criteria): array;


/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Region $Region): bool;

	public function Guardar(Region $Region): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_region): array|false;
	
    public function findById(int $id_region): ?Region;
	
    public function getNewId(): int;
}