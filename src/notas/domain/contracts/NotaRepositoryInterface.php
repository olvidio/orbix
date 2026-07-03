<?php

namespace src\notas\domain\contracts;

use src\notas\domain\entity\Nota;

/**
 * Interfaz de la clase Nota y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
interface NotaRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Nota $Nota): bool;

	public function Guardar(Nota $Nota): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_situacion
     * @return array<string, mixed>|false
	
     */
    public function datosById(int $id_situacion): array|false;
	
    /**
     * Busca la clase con id_situacion en el repositorio.
	
     */
    public function findById(int $id_situacion): ?Nota;
}