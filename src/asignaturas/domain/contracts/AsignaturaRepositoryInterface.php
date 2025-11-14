<?php

namespace src\asignaturas\domain\contracts;

use PDO;
use src\asignaturas\domain\entity\Asignatura;


use function core\is_true;
/**
 * Interfaz de la clase Asignatura y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface AsignaturaRepositoryInterface
{


    public function getJsonAsignaturas($aWhere): string;
    public function getArrayAsignaturasCreditos(): array;

    public function getArrayAsignaturasConSeparador(bool $op_genericas = true): array;
    public function getListaOpGenericas(string $formato = ''): string;
    public function getArrayAsignaturas(): array;

    public function getAsignaturasAsJson($aWhere = [], $aOperators = array()): string;
/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Asignatura
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Asignatura
	
	 */
	public function getAsignaturas(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Asignatura $Asignatura): bool;

	public function Guardar(Asignatura $Asignatura): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_asignatura
     * @return array|bool
	
     */
    public function datosById(int $id_asignatura): array|bool;
	
    /**
     * Busca la clase con id_asignatura en el repositorio.
	
     */
    public function findById(int $id_asignatura): ?Asignatura;
	
    public function getNewId();
}