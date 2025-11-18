<?php

namespace src\notas\domain\contracts;

use PDO;
use src\notas\domain\entity\Nota;


use function core\is_true;
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

    public function getArrayNotasNoSuperadas(): array;
    public function getArrayNotasSuperadas(): array;
    public function getArrayNotas(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Nota $Nota): bool;

	public function Guardar(Nota $Nota): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_situacion
     * @return array|bool
	
     */
    public function datosById(int $id_situacion): array|bool;
	
    /**
     * Busca la clase con id_situacion en el repositorio.
	
     */
    public function findById(int $id_situacion): ?Nota;
}