<?php

namespace src\actividades\domain\contracts;

use src\actividades\domain\entity\Importada;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase Importada y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface ImportadaRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * @return list<Importada>
	 */
	public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Importada $Importada): bool;

	public function Guardar(Importada $Importada): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ): array|false;
	
    /**
     * Busca la clase con id_activ en el repositorio.
     */
    public function findById(int $id_activ): ?Importada;
}
