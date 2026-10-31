<?php

namespace src\personas\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\personas\domain\entity\UltimaAsistencia;


/**
 * Interfaz de la clase UltimaAsistencia y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/1/2026
 */
interface UltimaAsistenciaRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * @return list<UltimaAsistencia>
	 */
	public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(UltimaAsistencia $UltimaAsistencia): bool;

	public function Guardar(UltimaAsistencia $UltimaAsistencia): bool;

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
    public function findById(int $id_item): ?UltimaAsistencia;
	
    public function getNewId(): int;
}
