<?php

namespace src\configuracion\domain\contracts;

use PDO;
use src\configuracion\domain\entity\ModuloInstalado;

use function core\is_true;
/**
 * Interfaz de la clase ModuloInstalado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
interface ModuloInstaladoRepositoryInterface
{

    public function getArrayModulosInstalados(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ModuloInstalado
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ModuloInstalado
	
	 */
	public function getModuloInstalados(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ModuloInstalado $ModuloInstalado): bool;

	public function Guardar(ModuloInstalado $ModuloInstalado): bool;

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
    public function findById(int $id_mod): ?ModuloInstalado;
}