<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoSacdHorario;


/**
 * Interfaz de la clase EncargoSacdHorario y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoSacdHorarioRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo EncargoSacdHorario
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<EncargoSacdHorario> Una colección de objetos de tipo EncargoSacdHorario
	
	 */
	public function getEncargoSacdHorarios(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(EncargoSacdHorario $EncargoSacdHorario): bool;

	public function Guardar(EncargoSacdHorario $EncargoSacdHorario): bool;

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
    public function findById(int $id_item): ?EncargoSacdHorario;
	
    public function getNewId(): int;

    /**
     * Filas en `encargo_sacd_horario_excepcion` para este horario.
     */
    public function countExcepcionesByHorarioId(int $id_item_h): int;

    /**
     * Elimina excepciones asociadas (p. ej. antes de borrar el horario sacd).
     */
    public function eliminarExcepcionesPorHorarioId(int $id_item_h): bool;
}