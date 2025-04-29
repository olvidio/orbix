<?php

namespace src\configuracion\domain\contracts;

use PDO;
use src\configuracion\domain\entity\Modulo;


/**
 * Interfaz de la clase Modulo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
interface ModuloRepositoryInterface
{

    public function getArrayModulos(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
     * devuelve una colección (array) de objetos de tipo Modulo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Modulo
     */
	public function getModulos(array $aWhere = [], array $aOperators = []): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Modulo $Modulo): bool;

	public function Guardar(Modulo $Modulo): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_mod
     * @return array|bool
     */
    public function datosById(int $id_mod): array|bool;
	
    /**
     * Busca la clase con id_mod en el repositorio.
     */
    public function findById(int $id_mod): ?Modulo;
	
    public function getNewId();
}