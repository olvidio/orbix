<?php

namespace src\profesores\domain\contracts;

use src\profesores\domain\entity\ProfesorTipo;

/**
 * Interfaz de la clase ProfesorTipo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface ProfesorTipoRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayProfesorTipos(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ProfesorTipo
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<ProfesorTipo> Una colección de objetos de tipo ProfesorTipo
	
	 */
	public function getProfesorTipos(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorTipo $ProfesorTipo): bool;

	public function Guardar(ProfesorTipo $ProfesorTipo): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_tipo_profesor
     * @return array<string, mixed>|false
	
     */
    public function datosById(int $id_tipo_profesor): array|false;
	
    /**
     * Busca la clase con id_tipo_profesor en el repositorio.
	
     */
    public function findById(int $id_tipo_profesor): ?ProfesorTipo;
	
    public function getNewId(): int;
}